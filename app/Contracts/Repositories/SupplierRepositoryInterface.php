<?php

namespace App\Contracts\Repositories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;

interface SupplierRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10, ?string $searchTerm = null);
    public function findById(int $id): ?Supplier;
    public function create(array $data): Supplier;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
