<?php

namespace App\Services;

use App\Contracts\Repositories\SupplierRepositoryInterface;
use App\Models\Supplier;
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

    public function getSupplierWithLayups(int $id): ?Supplier
    {
        $supplier = $this->supplierRepository->findById($id);
        if ($supplier) {
            $supplier->load('layups:id,supplier_id,name');
        }

        return $supplier;
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
     * Export single supplier with full hierarchy as structured array.
     */
    public function exportSupplier(int $id): array
    {
        $supplier = Supplier::with(['layups.layers'])->find($id);

        if (! $supplier) {
            throw new Exception('Supplier not found');
        }

        return [
            'layups' => $supplier->layups->map(fn($layup) => [
                'name'   => $layup->name,
                'layers' => $layup->layers->map(fn($layer) => [
                    'layer_order' => $layer->layer_order,
                    'thickness'   => (float) $layer->thickness,
                    'width'       => (float) $layer->width,
                    'angle'       => (float) $layer->angle,
                ]),
            ]),
        ];
    }

    public function exportFilename(Supplier $supplier): string
    {
        return 'CLT_Export_' . str_replace(' ', '_', $supplier->name) . '.json';
    }
}
