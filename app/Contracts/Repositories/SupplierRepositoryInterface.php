<?php

namespace App\Contracts\Repositories;

use App\Models\Supplier;

interface SupplierRepositoryInterface
{
    public function paginate(int $perPage = 10, ?string $searchTerm = null);

    public function findById(int $id): ?Supplier;

    public function create(array $data): Supplier;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
