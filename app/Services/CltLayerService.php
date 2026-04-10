<?php

namespace App\Services;

use App\Contracts\Repositories\CltLayerRepositoryInterface;
use App\Models\CltLayer;

class CltLayerService
{
    protected $repository;

    public function __construct(CltLayerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(?string $searchTerm = null, int $perPage = 10)
    {
        return $this->repository->paginate($perPage, $searchTerm);
    }

    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): CltLayer
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
