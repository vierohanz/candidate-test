<?php

namespace App\Contracts\Repositories;

use App\Models\CltLayup;
use Illuminate\Database\Eloquent\Collection;

interface CltLayupRepositoryInterface
{
    public function paginate(int $perPage = 10, ?string $searchTerm = null);
    public function findById(int $id): ?CltLayup;
    public function create(array $data): CltLayup;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByNameUnderSupplier(string $name, int $supplierId): ?CltLayup;
}
