<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Standard success response structure
     */
    protected function successResponse($data = [], string $message = 'success', int $code = 200, array $metadata = []): JsonResponse
    {
        if (is_object($data) && method_exists($data, 'items') && method_exists($data, 'currentPage')) {
            $metadata = array_merge([
                'per_page' => method_exists($data, 'perPage') ? $data->perPage() : 10,
                'current_page' => $data->currentPage(),
                'total_row' => method_exists($data, 'total') ? $data->total() : $data->count(),
                'total_page' => method_exists($data, 'lastPage') ? $data->lastPage() : 1,
            ], $metadata);

            $data = $data->items();
        }

        if (is_null($data)) {
            $data = new \stdClass;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'metadata' => (object) $metadata,
            'data' => $data,
        ], $code);
    }

    /**
     * Standard error response structure
     */
    protected function errorResponse(string $message = 'error', int $code = 400, $data = null, array $metadata = []): JsonResponse
    {
        if (is_null($data) || (is_array($data) && empty($data))) {
            $data = new \stdClass;
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'metadata' => (object) $metadata,
            'data' => $data,
        ], $code);
    }
}
