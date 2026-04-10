<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Http\Resources\SupplierWithFullHierarchyResource;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $searchTerm = trim($request->query('q'));
        $perPage = (int) $request->query('per_page', 10);
        $suppliers = $this->supplierService->getAllSuppliers($searchTerm, $perPage);
        
        // Wrap the items in Resource while keeping paginator for metadata extraction
        $suppliers->setCollection(
            SupplierResource::collection($suppliers->getCollection())->collection
        );

        return $this->successResponse(
            $suppliers,
            'Suppliers retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request): JsonResponse
    {
        $this->supplierService->createSupplier($request->validated());

        return $this->successResponse(
            null,
            'Supplier created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): JsonResponse
    {
        return $this->successResponse(
            new SupplierResource($supplier),
            'Supplier retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $updated = $this->supplierService->updateSupplier($supplier->id, $request->validated());

        if (!$updated) {
            return $this->errorResponse('Supplier update failed', 500);
        }

        return $this->successResponse(
            null,
            'Supplier updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        $deleted = $this->supplierService->deleteSupplier($supplier->id);

        if (!$deleted) {
            return $this->errorResponse('Supplier deletion failed', 500);
        }

        return $this->successResponse(null, 'Supplier deleted successfully');
    }

    /**
     * Export hierarchical data for a specific supplier as a downloadable JSON file.
     */
    public function export(Supplier $supplier): \Symfony\Component\HttpFoundation\Response
    {
        // Refresh with relations
        $supplier->load('layups.layers');

        $resource = new SupplierWithFullHierarchyResource($supplier);
        $json = json_encode($resource->resolve(), JSON_PRETTY_PRINT);
        
        $filename = "export_supplier_" . Str::slug($supplier->name) . "_" . date('Ymd_His') . ".json";

        return response($json, 200, [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
