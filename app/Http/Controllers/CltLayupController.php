<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayupRequest;
use App\Http\Resources\CltLayupResource;
use App\Models\CltLayup;
use App\Contracts\Repositories\CltLayupRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CltLayupController extends Controller
{
    protected $layupRepository;

    public function __construct(CltLayupRepositoryInterface $layupRepository)
    {
        $this->layupRepository = $layupRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $supplier_id): JsonResponse
    {
        $searchTerm = trim($request->query('q'));
        $perPage = (int) $request->query('per_page', 10);

        $layups = CltLayup::where('supplier_id', (int) $supplier_id)
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where('name', 'ILIKE', "%{$searchTerm}%");
            })
            ->latest()
            ->paginate($perPage);
        
        // Wrap collection in Resource
        $layups->setCollection(
            CltLayupResource::collection($layups->getCollection())->collection
        );

        return $this->successResponse($layups, 'Layups retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CltLayupRequest $request): JsonResponse
    {
        $this->layupRepository->create($request->validated());

        return $this->successResponse(
            null,
            'Layup created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(CltLayup $layup): JsonResponse
    {
        return $this->successResponse(
            new CltLayupResource($layup),
            'Layup retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CltLayupRequest $request, CltLayup $layup): JsonResponse
    {
        $updated = $this->layupRepository->update($layup->id, $request->validated());

        if (!$updated) {
            return $this->errorResponse('Layup update failed', 500);
        }

        return $this->successResponse(
            null,
            'Layup updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CltLayup $layup): JsonResponse
    {
        $deleted = $this->layupRepository->delete($layup->id);

        if (!$deleted) {
            return $this->errorResponse('Layup deletion failed', 500);
        }

        return $this->successResponse(null, 'Layup deleted successfully');
    }
}
