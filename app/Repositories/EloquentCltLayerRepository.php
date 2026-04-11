<?php

namespace App\Repositories;

use App\Contracts\Repositories\CltLayerRepositoryInterface;
use App\Models\CltLayer;

class EloquentCltLayerRepository implements CltLayerRepositoryInterface
{
    public function paginate(int $perPage = 10, ?string $searchTerm = null, ?int $supplierId = null, ?int $layupId = null)
    {
        // Search by layer_order or related layup name
        return CltLayer::with('layup.supplier')
            ->when($layupId, function ($q, $lid) {
                return $q->where('layup_id', $lid);
            })
            ->when($supplierId, function ($q, $sid) {
                return $q->whereHas('layup', function ($q2) use ($sid) {
                    $q2->where('supplier_id', $sid);
                });
            })
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where('layer_order', (int) $searchTerm)
                    ->orWhereHas('layup', function ($q) use ($searchTerm) {
                        $q->where('name', 'ILIKE', "%{$searchTerm}%");
                    });
            })
            ->orderBy('layup_id')
            ->orderBy('layer_order')
            ->paginate($perPage);
    }

    public function findById(int $id): ?CltLayer
    {
        return CltLayer::with('layup.supplier')->find($id);
    }

    public function create(array $data): CltLayer
    {
        return CltLayer::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $layer = CltLayer::find($id);
        if (! $layer) {
            return false;
        }

        return $layer->update($data);
    }

    public function delete(int $id): bool
    {
        $layer = CltLayer::find($id);
        if (! $layer) {
            return false;
        }

        return $layer->delete();
    }
}
