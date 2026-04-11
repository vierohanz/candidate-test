<?php

namespace App\Services;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportService
{
    /**
     * Phase 1: Scan and Detect Conflicts (Dry Run)
     * Stores result in Cache for 1 hour.
     */
    public function scan(int $supplierId, array $data): array
    {
        $supplier = Supplier::findOrFail($supplierId);
        $token = Str::uuid()->toString();

        $report = [
            'import_token' => $token,
            'layups' => [
                'new' => [],
                'matches' => [],
                'conflicts' => [],
            ],
            'layers' => [
                'new' => [],
                'conflicts' => [],
            ],
            'summary' => [
                'total_layups' => 0,
                'total_layers' => 0,
                'has_conflicts' => false,
            ],
        ];

        // Store the actual raw data in cache, using the token as key
        Cache::put("import_data_{$token}", $data, now()->addHour());

        $layups = $data['layups'] ?? [];

        foreach ($layups as $layupData) {
            $report['summary']['total_layups']++;
            $layupName = trim($layupData['name'] ?? 'Unnamed');
            $existingLayup = CltLayup::where('supplier_id', $supplier->id)
                ->whereRaw('LOWER(name) = LOWER(?)', [$layupName])
                ->whereNull('deleted_at')
                ->first();

            $currentLayupId = $existingLayup ? $existingLayup->id : null;

            if ($existingLayup) {
                $report['layups']['matches'][] = "Layup [{$layupName}] already exists (will be used/updated)";
            } else {
                $report['layups']['new'][] = ['name' => $layupName];
            }

            // Process Layers
            $layers = $layupData['layers'] ?? [];
            foreach ($layers as $layerData) {
                $report['summary']['total_layers']++;
                $order = $layerData['layer_order'] ?? 0;

                $existingLayer = null;
                if ($currentLayupId) {
                    $existingLayer = CltLayer::where('layup_id', $currentLayupId)
                        ->where('layer_order', $order)
                        ->whereNull('deleted_at')
                        ->first();
                }

                $incomingSpecs = [
                    'thickness' => (float) ($layerData['thickness'] ?? 0),
                    'width' => (float) ($layerData['width'] ?? 0),
                    'angle' => (float) ($layerData['angle'] ?? 0),
                ];

                if ($existingLayer) {
                    $isDifferent =
                        ($existingLayer->thickness != $incomingSpecs['thickness']) ||
                        ($existingLayer->width != $incomingSpecs['width']) ||
                        ($existingLayer->angle != $incomingSpecs['angle']);

                    if ($isDifferent) {
                        $report['layers']['conflicts'][] = [
                            'layup_name' => $layupName,
                            'layer_order' => $order,
                            'existing' => [
                                'thickness' => (float) $existingLayer->thickness,
                                'width' => (float) $existingLayer->width,
                                'angle' => (float) $existingLayer->angle,
                            ],
                            'incoming' => $incomingSpecs,
                        ];
                        $report['summary']['has_conflicts'] = true;
                    } else {
                        $report['layups']['matches'][] = "Layup [{$layupName}] Layer [{$order}] matches exactly";
                    }
                } else {
                    $report['layers']['new'][] = [
                        'layup_name' => $layupName,
                        'layer_order' => $order,
                        'specs' => $incomingSpecs,
                    ];
                }
            }
        }

        return $report;
    }

    /**
     * Phase 2: Actual Import Execution with chosen strategy using cached token.
     */
    public function execute(int $supplierId, string $token, string $strategy = 'skip', array $resolutions = []): array
    {
        $supplier = Supplier::findOrFail($supplierId);
        $data = Cache::get("import_data_{$token}");

        if (! $data) {
            throw new Exception('Import data expired or token invalid.');
        }

        $summary = [
            'layups_created' => 0,
            'layups_updated' => 0,
            'layups_skipped' => 0,
            'layers_created' => 0,
            'layers_updated' => 0,
            'layers_skipped' => 0,
        ];

        DB::transaction(function () use ($supplier, $data, $strategy, $resolutions, &$summary) {
            $layups = $data['layups'] ?? [];

            foreach ($layups as $layupData) {
                $layupName = trim($layupData['name'] ?? '');
                if (! $layupName) {
                    continue;
                }

                $existingLayup = CltLayup::where('supplier_id', $supplier->id)
                    ->whereRaw('LOWER(name) = LOWER(?)', [$layupName])
                    ->whereNull('deleted_at')
                    ->first();

                if ($existingLayup) {
                    if ($strategy === 'duplicate') {
                        $duplicateName = $layupName . ' (Imported)';
                        $currentLayup = CltLayup::create([
                            'supplier_id' => $supplier->id,
                            'name' => $duplicateName,
                        ]);
                        $summary['layups_created']++;
                    } elseif ($strategy === 'overwrite') {
                        $existingLayup->update(['name' => $layupName]);
                        $summary['layups_updated']++;
                        $currentLayup = $existingLayup;
                    } else {
                        $summary['layups_skipped']++;
                        $currentLayup = $existingLayup;
                    }
                } else {
                    $currentLayup = CltLayup::create([
                        'supplier_id' => $supplier->id,
                        'name' => $layupName,
                    ]);
                    $summary['layups_created']++;
                }

                // Process Layers
                $layers = $layupData['layers'] ?? [];
                foreach ($layers as $layerData) {
                    $order = $layerData['layer_order'] ?? null;
                    if ($order === null) {
                        continue;
                    }

                    $existingLayer = CltLayer::where('layup_id', $currentLayup->id)
                        ->where('layer_order', $order)
                        ->whereNull('deleted_at')
                        ->first();

                    $payload = [
                        'thickness' => (float) $layerData['thickness'],
                        'width' => (float) $layerData['width'],
                        'angle' => (float) $layerData['angle'],
                    ];

                    if ($existingLayer) {
                        $isDifferent =
                            ($existingLayer->thickness != $payload['thickness']) ||
                            ($existingLayer->width != $payload['width']) ||
                            ($existingLayer->angle != $payload['angle']);

                        if ($isDifferent) {
                            $resolutionKey = "{$layupName}_{$order}";
                            $fieldsToUpdate = $resolutions[$resolutionKey] ?? null;

                            // Handle both legacy (bool/array of keys) and new (key => [fields]) formats
                            // If strategy is overwrite, or if this key exists in a simple list, or if it has specific fields
                            $shouldOverwriteAll = ($strategy === 'overwrite') || (is_numeric(array_search($resolutionKey, $resolutions)));

                            if ($shouldOverwriteAll) {
                                $existingLayer->update($payload);
                                $summary['layers_updated']++;
                            } elseif (is_array($fieldsToUpdate)) {
                                // Field-level resolution
                                $granularPayload = [];
                                foreach ($fieldsToUpdate as $field) {
                                    if (array_key_exists($field, $payload)) {
                                        $granularPayload[$field] = $payload[$field];
                                    }
                                }

                                if (! empty($granularPayload)) {
                                    $existingLayer->update($granularPayload);
                                    $summary['layers_updated']++;
                                } else {
                                    $summary['layers_skipped']++;
                                }
                            } else {
                                $summary['layers_skipped']++;
                            }
                        } else {
                            $summary['layers_skipped']++;
                        }
                    } else {
                        $currentLayup->layers()->create(array_merge($payload, ['layer_order' => $order]));
                        $summary['layers_created']++;
                    }
                }
            }
        });

        // Invalidate token after successful use
        Cache::forget("import_data_{$token}");

        return $summary;
    }
}
