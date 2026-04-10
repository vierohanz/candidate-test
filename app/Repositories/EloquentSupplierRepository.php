<?php

namespace App\Repositories;

use App\Contracts\Repositories\SupplierRepositoryInterface;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;

class EloquentSupplierRepository implements SupplierRepositoryInterface
{
    public function all(): Collection
    {
        return Supplier::select(['id', 'name'])->get();
    }

    public function paginate(int $perPage = 10, ?string $searchTerm = null)
    {
        return Supplier::select(['id', 'name'])
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where('name', 'ILIKE', "%{$searchTerm}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Supplier
    {
        return Supplier::find($id);
    }

    public function create(array $data): Supplier
    {
        return Supplier::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $supplier = $this->findById($id);
        if (!$supplier) {
            return false;
        }
        return $supplier->update($data);
    }

    public function delete(int $id): bool
    {
        $supplier = $this->findById($id);
        if (!$supplier) {
            return false;
        }
        return $supplier->delete();
    }
}
