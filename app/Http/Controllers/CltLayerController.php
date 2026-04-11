<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayerRequest;
use App\Services\CltLayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CltLayerController extends Controller
{
    protected $layerService;

    public function __construct(CltLayerService $layerService)
    {
        $this->layerService = $layerService;
    }

    public function index(Request $request)
    {
        if (!$request->is('api/*')) {
            return view('layers.index');
        }

        $sid = $request->route('supplier_id') ?? $request->get('supplier_id');
        $lid = $request->route('layup_id') ?? $request->get('layup_id');

        if (!$sid || !\App\Models\Supplier::where('id', $sid)->exists()) {
            return $this->errorResponse('Supplier ID and Layup ID are required.', 422);
        }

        if (!$lid || !\App\Models\CltLayup::where('id', $lid)->exists()) {
            return $this->errorResponse('Supplier ID and Layup ID are required.', 422);
        }

        $layers = $this->layerService->getAll(
            $request->get('get_q'),
            $request->get('per_page', 10),
            $sid,
            $lid
        );

        $layers->setCollection(
            $layers->getCollection()->transform(fn($layer) => [
                'id'          => $layer->id,
                'layup_id'    => $layer->layup_id,
                'layer_order' => $layer->layer_order,
                'thickness'   => $layer->thickness,
                'width'       => $layer->width,
                'angle'       => $layer->angle,
                'layup'       => $layer->layup ? [
                    'name'        => $layer->layup->name,
                    'supplier_id' => $layer->layup->supplier_id,
                    'supplier'    => $layer->layup->supplier ? [
                        'id'   => $layer->layup->supplier->id,
                        'name' => $layer->layup->supplier->name,
                    ] : null,
                ] : null,
            ])->values()
        );

        return $this->successResponse($layers, 'Layers retrieved successfully');
    }

    public function show($id): JsonResponse
    {
        $layer = $this->layerService->getById($id);
        if (!$layer) {
            return $this->errorResponse('Layer not found', 404);
        }

        return $this->successResponse([
            'id'          => $layer->id,
            'layer_order' => $layer->layer_order,
            'thickness'   => $layer->thickness,
            'width'       => $layer->width,
            'angle'       => $layer->angle,
            'layup'       => $layer->layup ? [
                'id'            => $layer->layup->id,
                'name'          => $layer->layup->name,
                'supplier_name' => $layer->layup->supplier->name ?? 'N/A',
            ] : null,
        ], 'Layer retrieved successfully');
    }

    public function store(CltLayerRequest $request): JsonResponse
    {
        $this->layerService->create($request->validated());

        return $this->successResponse(null, 'Layer created successfully', 201);
    }

    public function update(CltLayerRequest $request, $id): JsonResponse
    {
        $updated = $this->layerService->update($id, $request->validated());
        if (!$updated) {
            return $this->errorResponse('Layer not found', 404);
        }

        return $this->successResponse(null, 'Layer updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->layerService->delete($id);
        if (!$deleted) {
            return $this->errorResponse('Layer not found', 404);
        }

        return $this->successResponse(null, 'Layer removed successfully');
    }
}
