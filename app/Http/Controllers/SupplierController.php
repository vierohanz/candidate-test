<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index(Request $request)
    {
        if (!$request->is('api/*')) {
            return view('suppliers.index');
        }

        $suppliers = $this->supplierService->getAllSuppliers(
            $request->get('q'),
            $request->get('per_page', 10)
        );

        $suppliers->setCollection(
            $suppliers->getCollection()->transform(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
            ])->values()
        );

        return $this->successResponse($suppliers, 'Suppliers retrieved successfully');
    }

    public function store(SupplierRequest $request): JsonResponse
    {
        $this->supplierService->createSupplier($request->validated());

        return $this->successResponse(null, 'Supplier created successfully', 201);
    }

    public function show($id): JsonResponse
    {
        $supplier = $this->supplierService->getSupplierWithLayups($id);
        if (!$supplier) {
            return $this->errorResponse('Supplier not found', 404);
        }

        return $this->successResponse([
            'id'     => $supplier->id,
            'name'   => $supplier->name,
            'layups' => $supplier->layups->map(fn($l) => ['id' => $l->id, 'name' => $l->name]),
        ], 'Supplier retrieved successfully');
    }

    public function update(SupplierRequest $request, $id): JsonResponse
    {
        $updated = $this->supplierService->updateSupplier($id, $request->validated());
        if (!$updated) {
            return $this->errorResponse('Supplier not found', 404);
        }

        return $this->successResponse(null, 'Supplier updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->supplierService->deleteSupplier($id);
        if (!$deleted) {
            return $this->errorResponse('Supplier not found', 404);
        }

        return $this->successResponse(null, 'Supplier deleted successfully');
    }

    public function export($id)
    {
        $supplier = $this->supplierService->getSupplierWithLayups($id);
        if (!$supplier) {
            return $this->errorResponse('Supplier not found', 404);
        }

        $data     = $this->supplierService->exportSupplier($id);
        $fileName = $this->supplierService->exportFilename($supplier);

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, $fileName, ['Content-Type' => 'application/json']);
    }
}
