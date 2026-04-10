<?php

namespace App\Contracts\Repositories;

use App\Models\CltLayer;

interface CltLayerRepositoryInterface
{
    public function paginate(int $perPage = 10, ?string $searchTerm = null);
    public function findById(int $id): ?CltLayer;
    public function create(array $data): CltLayer;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
