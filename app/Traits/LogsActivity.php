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
        $description = "[{$entityType}] {$action} — id:{$model->getKey()}";

        try {
            ActivityLog::create([
                'action'      => $action,
                'entity_type' => $entityType,
                'entity_id'   => $model->getKey(),
                'before'      => $before,
                'after'       => $after,
                'ip_address'  => Request::ip(),
                'description' => $description,
            ]);
        } catch (\Throwable $e) {
            // Never let logging failure break the main flow
            Log::error('ActivityLog write failed: ' . $e->getMessage());
        }

        // Mirror to the application log file as well
        Log::info($description, [
            'before' => $before,
            'after'  => $after,
        ]);
    }

    private static function resolveEntityType(): string
    {
        // Map fully-qualified class name → human-readable entity slug
        $map = [
            \App\Models\Supplier::class => 'supplier',
            \App\Models\CltLayup::class => 'clt_layup',
            \App\Models\CltLayer::class => 'clt_layer',
        ];

        return $map[static::class] ?? class_basename(static::class);
    }
}
