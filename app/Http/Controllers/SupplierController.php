<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Services\SupplierService;
use App\Support\ApiPageCache;
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

        $search = $request->get('q');
        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);

        $suppliers = ApiPageCache::remember('suppliers', [
            'q' => $search,
            'per_page' => $perPage,
            'page' => $page,
        ], 30, function () use ($search, $perPage) {
            return $this->supplierService->getAllSuppliers($search, $perPage);
        });

        $suppliers->setCollection(
            $suppliers->getCollection()->transform(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'layups_count' => $s->layups_count,
            ])->values()
        );

        return $this->successResponse($suppliers, 'Suppliers retrieved successfully');
    }

    public function store(SupplierRequest $request): JsonResponse
    {
        $this->supplierService->createSupplier($request->validated());
        ApiPageCache::bump(['suppliers', 'dashboard', 'activity_logs', 'layups', 'layers']);

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
        ApiPageCache::bump(['suppliers', 'dashboard', 'activity_logs', 'layups', 'layers']);

        return $this->successResponse(null, 'Supplier updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->supplierService->deleteSupplier($id);
        if (!$deleted) {
            return $this->errorResponse('Supplier not found', 404);
        }
        ApiPageCache::bump(['suppliers', 'dashboard', 'activity_logs', 'layups', 'layers']);

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
