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
        // If data is a Paginated result, extract metadata
        if ($data instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            $metadata = array_merge([
                'per_page'     => $data->perPage(),
                'current_page' => $data->currentPage(),
                'total_row'    => $data->total(),
                'total_page'   => $data->lastPage(),
            ], $metadata);

            $data = $data->items();
        }

        return response()->json([
            'success'  => true,
            'message'  => $message,
            'metadata' => (object) $metadata,
            'data'     => $data,
        ], $code);
    }

    /**
     * Standard error response structure
     */
    protected function errorResponse(string $message = 'error', int $code = 400, $data = [], array $metadata = []): JsonResponse
    {
        return response()->json([
            'success'  => false,
            'message'  => $message,
            'metadata' => (object) $metadata,
            'data'     => $data,
        ], $code);
    }
}
