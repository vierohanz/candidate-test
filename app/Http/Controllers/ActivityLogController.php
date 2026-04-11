<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Support\ApiPageCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $searchTerm = trim((string) $request->query('q', ''));
        $action = trim((string) $request->query('action', ''));
        $entityType = trim((string) $request->query('entity_type', ''));
        $perPage = (int) $request->query('per_page', 10);

        $logs = ApiPageCache::remember('activity_logs', [
            'q' => $searchTerm,
            'action' => $action,
            'entity_type' => $entityType,
            'per_page' => $perPage,
            'page' => (int) $request->query('page', 1),
        ], 30, function () use ($searchTerm, $action, $entityType, $perPage) {
            return ActivityLog::query()
                ->when($searchTerm, function ($query, $searchTerm) {
                    $query->where(function ($inner) use ($searchTerm) {
                        $inner->where('description', 'ILIKE', "%{$searchTerm}%")
                            ->orWhere('action', 'ILIKE', "%{$searchTerm}%")
                            ->orWhere('entity_type', 'ILIKE', "%{$searchTerm}%");
                    });
                })
                ->when($action, fn ($query) => $query->whereRaw('LOWER(action) = ?', [mb_strtolower($action)]))
                ->when($entityType, fn ($query) => $query->whereRaw('LOWER(entity_type) = ?', [mb_strtolower($entityType)]))
                ->latest('created_at')
                ->paginate(max($perPage, 1));
        });

        return $this->successResponse($logs, 'Activity logs retrieved successfully');
    }
}
