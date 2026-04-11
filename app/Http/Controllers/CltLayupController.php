<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayupRequest;
use App\Services\CltLayupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CltLayupController extends Controller
{
    protected $layupService;

    public function __construct(CltLayupService $layupService)
    {
        $this->layupService = $layupService;
    }

    public function index(Request $request)
    {
        if (!$request->is('api/*')) {
            return view('layups.index');
        }

        $supplierId = $request->route('supplier_id') ?? $request->get('supplier_id');

        if (!$supplierId || !\App\Models\Supplier::where('id', $supplierId)->exists()) {
            return $this->errorResponse('Supplier not found', 422);
        }

        $layups = $this->layupService->getAll(
            $request->get('q'),
            $request->get('per_page', 10),
            $supplierId
        );

        $layups->setCollection(
            $layups->getCollection()->transform(fn($layup) => [
                'id'          => $layup->id,
                'name'        => $layup->name,
                'supplier_id' => $layup->supplier_id,
                'supplier'    => $layup->supplier ? ['name' => $layup->supplier->name] : null,
                'layers_count' => $layup->layers_count,
            ])->values()
        );

        return $this->successResponse($layups, 'Layups retrieved successfully');
    }

    public function show($id): JsonResponse
    {
        $layup = $this->layupService->getById($id);
        if (!$layup) {
            return $this->errorResponse('Layup not found', 404);
        }

        return $this->successResponse([
            'id'          => $layup->id,
            'name'        => $layup->name,
            'supplier_id' => $layup->supplier_id,
            'supplier'    => $layup->supplier ? [
                'id'   => $layup->supplier->id,
                'name' => $layup->supplier->name,
            ] : null,
            'layers' => $layup->layers->map(fn($l) => [
                'id'        => $l->id,
                'order'     => $l->layer_order,
                'thickness' => $l->thickness,
                'width'     => $l->width,
                'angle'     => $l->angle,
            ]),
        ], 'Layup retrieved successfully');
    }

    public function store(CltLayupRequest $request): JsonResponse
    {
        $this->layupService->create($request->validated());

        return $this->successResponse(null, 'Layup created successfully', 201);
    }

    public function update(CltLayupRequest $request, $id): JsonResponse
    {
        $updated = $this->layupService->update($id, $request->validated());
        if (!$updated) {
            return $this->errorResponse('Layup not found', 404);
        }

        return $this->successResponse(null, 'Layup updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->layupService->delete($id);
        if (!$deleted) {
            return $this->errorResponse('Layup not found', 404);
        }

        return $this->successResponse(null, 'Layup deleted successfully');
    }
}
