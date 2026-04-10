<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayerRequest;
use App\Http\Resources\CltLayerResource;
use App\Models\CltLayer;
use App\Services\CltLayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CltLayerController extends Controller
{
    protected $service;

    public function __construct(CltLayerService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $supplier_id = null, $layup_id = null): JsonResponse
    {
        // Use layup_id if provided, otherwise you might want to list all layers for a supplier?
        // But the requirement says "layers of specific layup".
        
        $searchTerm = trim($request->query('q'));
        $perPage = (int) $request->query('per_page', 10);

        $query = CltLayer::query();

        if ($layup_id) {
            $query->where('layup_id', (int) $layup_id);
        }

        $layers = $query->latest()->paginate($perPage);
        
        $layers->setCollection(
            CltLayerResource::collection($layers->getCollection())->collection
        );

        return $this->successResponse($layers, 'Layers retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CltLayerRequest $request): JsonResponse
    {
        $this->service->create($request->validated());

        return $this->successResponse(
            null,
            'Layer created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(CltLayer $layer): JsonResponse
    {
        return $this->successResponse(
            new CltLayerResource($layer),
            'Layer retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CltLayerRequest $request, CltLayer $layer): JsonResponse
    {
        $updated = $this->service->update($layer->id, $request->validated());

        if (!$updated) {
            return $this->errorResponse('Layer update failed', 500);
        }

        return $this->successResponse(
            null,
            'Layer updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CltLayer $layer): JsonResponse
    {
        $deleted = $this->service->delete($layer->id);

        if (!$deleted) {
            return $this->errorResponse('Layer deletion failed', 500);
        }

        return $this->successResponse(null, 'Layer deleted successfully');
    }
}
