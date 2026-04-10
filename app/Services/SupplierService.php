<?php

namespace App\Services;

use App\Contracts\Repositories\SupplierRepositoryInterface;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Exception;

class SupplierService
{
    protected $supplierRepository;

    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function getAllSuppliers(?string $searchTerm = null, int $perPage = 10)
    {
        return $this->supplierRepository->paginate($perPage, $searchTerm);
    }

    public function getSupplierById(int $id)
    {
        return $this->supplierRepository->findById($id);
    }

    public function createSupplier(array $data): Supplier
    {
        return $this->supplierRepository->create($data);
    }

    public function updateSupplier(int $id, array $data): bool
    {
        return $this->supplierRepository->update($id, $data);
    }

    public function deleteSupplier(int $id): bool
    {
        return $this->supplierRepository->delete($id);
    }

    /**
     * Export single supplier with full hierarchy
     */
    public function exportSupplier(int $id)
    {
        $supplier = Supplier::with(['layups.layers'])->find($id);
        
        if (!$supplier) {
            throw new Exception("Supplier not found");
        }

        return $supplier->toArray();
    }
}
