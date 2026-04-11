<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierService;

class ExportController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function download(Supplier $supplier)
    {
        $data     = $this->supplierService->exportSupplier($supplier->id);
        $fileName = $this->supplierService->exportFilename($supplier);

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, $fileName, ['Content-Type' => 'application/json']);
    }
}
