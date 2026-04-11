<?php

namespace App\Providers;

use App\Contracts\Repositories\CltLayerRepositoryInterface;
use App\Contracts\Repositories\CltLayupRepositoryInterface;
use App\Contracts\Repositories\SupplierRepositoryInterface;
use App\Repositories\EloquentCltLayerRepository;
use App\Repositories\EloquentCltLayupRepository;
use App\Repositories\EloquentSupplierRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(SupplierRepositoryInterface::class, EloquentSupplierRepository::class);
        $this->app->bind(CltLayupRepositoryInterface::class, EloquentCltLayupRepository::class);
        $this->app->bind(CltLayerRepositoryInterface::class, EloquentCltLayerRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
