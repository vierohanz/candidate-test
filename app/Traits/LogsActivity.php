<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * Automatically logs create / update / delete events on any Eloquent model
 * to both the activity_logs table and the application log file.
 *
 * Usage: add `use LogsActivity;` inside your model class.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(fn (Model $model) => static::recordActivity('created', $model, null, $model->toArray()));

        static::updated(function (Model $model) {
            $before = collect($model->getOriginal())
                ->only(array_keys($model->getDirty()))
                ->toArray();

            $after = collect($model->getDirty())->toArray();

            static::recordActivity('updated', $model, $before, $after);
        });

        static::deleted(fn (Model $model) => static::recordActivity('deleted', $model, $model->toArray(), null));
    }

    private static function recordActivity(string $action, Model $model, ?array $before, ?array $after): void
    {
        $entityType = static::resolveEntityType();
        $description = static::generateActivityDescription($action, $model, $before, $after);

        try {
            ActivityLog::create([
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $model->getKey(),
                'before' => $before,
                'after' => $after,
                'ip_address' => Request::ip(),
                'description' => $description,
            ]);
        } catch (\Throwable $e) {
            Log::error('ActivityLog write failed: '.$e->getMessage());
        }

        Log::info("[{$entityType}] {$action} — id:{$model->getKey()}", [
            'description' => $description,
            'before' => $before,
            'after' => $after,
        ]);
    }

    private static function generateActivityDescription(string $action, Model $model, ?array $before, ?array $after): string
    {
        $type = ucfirst(str_replace('_', ' ', static::resolveEntityType()));
        $name = $model->name ?? ($model->layer_order ? "Layer {$model->layer_order}" : 'Item');

        // Context enrichment (Supplier name for Layups, etc.)
        $context = '';
        if ($model instanceof \App\Models\CltLayup) {
            $supplierName = $model->supplier->name ?? 'Unknown Supplier';
            $context = " for Supplier [{$supplierName}]";
        } elseif ($model instanceof \App\Models\CltLayer) {
            $layupName = $model->layup->name ?? 'Unknown Layup';
            $context = " in Layup [{$layupName}]";
        }

        if ($action === 'created') {
            return "{$type} [{$name}] successfully created{$context}";
        }

        if ($action === 'deleted') {
            return "{$type} [{$name}] successfully deleted{$context}";
        }

        if ($action === 'updated') {
            $oldName = $before['name'] ?? $name;
            $newName = $after['name'] ?? $name;

            if (isset($after['name']) && isset($before['name'])) {
                return "{$type} name changed from [{$oldName}] to [{$newName}]{$context}";
            }

            return "{$type} [{$name}] updated{$context}";
        }

        return "{$type} activity: {$action}{$context}";
    }

    private static function resolveEntityType(): string
    {
        $map = [
            \App\Models\Supplier::class => 'Supplier',
            \App\Models\CltLayup::class => 'Layup',
            \App\Models\CltLayer::class => 'Layer',
        ];

        return $map[static::class] ?? class_basename(static::class);
    }
}
