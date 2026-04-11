<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
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

        $logs = ActivityLog::query()
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($inner) use ($searchTerm) {
                    $inner->where('description', 'ILIKE', "%{$searchTerm}%")
                        ->orWhere('action', 'ILIKE', "%{$searchTerm}%")
                        ->orWhere('entity_type', 'ILIKE', "%{$searchTerm}%");
                });
            })
            ->when($action, fn ($query) => $query->where('action', $action))
            ->when($entityType, fn ($query) => $query->where('entity_type', $entityType))
            ->latest('created_at')
            ->paginate(max($perPage, 1));

        return $this->successResponse($logs, 'Activity logs retrieved successfully');
    }
}
