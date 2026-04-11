<?php

namespace App\Repositories;

use App\Contracts\Repositories\CltLayupRepositoryInterface;
use App\Models\CltLayup;

class EloquentCltLayupRepository implements CltLayupRepositoryInterface
{
    public function paginate(int $perPage = 10, ?string $searchTerm = null, ?int $supplierId = null)
    {
        return CltLayup::with('supplier')
            ->when($supplierId, function ($q, $sid) {
                return $q->where('supplier_id', $sid);
            })
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where('name', 'ILIKE', "%{$searchTerm}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?CltLayup
    {
        return CltLayup::with(['supplier', 'layers'])->find($id);
    }

    public function create(array $data): CltLayup
    {
        return CltLayup::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $layup = CltLayup::find($id);
        if (! $layup) {
            return false;
        }

        return $layup->update($data);
    }

    public function delete(int $id): bool
    {
        $layup = CltLayup::find($id);
        if (! $layup) {
            return false;
        }

        return $layup->delete();
    }

}
