<?php

namespace App\Services;

use App\Contracts\Repositories\CltLayupRepositoryInterface;
use App\Models\CltLayup;

class CltLayupService
{
    protected $repository;

    public function __construct(CltLayupRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(?string $searchTerm = null, int $perPage = 10, ?int $supplierId = null)
    {
        return $this->repository->paginate($perPage, $searchTerm, $supplierId);
    }

    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): CltLayup
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
