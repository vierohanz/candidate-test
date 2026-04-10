<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\CltLayerController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Suppliers
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('suppliers/{supplier}/show', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::put('suppliers/{supplier}/update', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('suppliers/{supplier}/delete', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::get('suppliers/{supplier}/export', [SupplierController::class, 'export'])->name('suppliers.export');
    Route::post('suppliers/{supplier}/import/scan', [ImportController::class, 'scan'])->name('suppliers.import.scan');
    Route::post('suppliers/{supplier}/import/confirm', [ImportController::class, 'confirm'])->name('suppliers.import.confirm');

    // Layups
    Route::post('layups', [CltLayupController::class, 'store'])->name('layups.store');
    Route::get('layups/{layup}/show', [CltLayupController::class, 'show'])->name('layups.show');
    Route::put('layups/{layup}/update', [CltLayupController::class, 'update'])->name('layups.update');
    Route::delete('layups/{layup}/delete', [CltLayupController::class, 'destroy'])->name('layups.destroy');

    // Layers
    Route::post('layers', [CltLayerController::class, 'store'])->name('layers.store');
    Route::get('layers/{layer}/show', [CltLayerController::class, 'show'])->name('layers.show');
    Route::put('layers/{layer}/update', [CltLayerController::class, 'update'])->name('layers.update');
    Route::delete('layers/{layer}/delete', [CltLayerController::class, 'destroy'])->name('layers.destroy');

    // Listing with Parent Parameters - MUST BE TRIGGERED LAST
    Route::get('suppliers',                    [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('layups/{supplier_id}',         [CltLayupController::class, 'index'])->name('layups.index');
    Route::get('layers/{supplier_id}/{layup_id}', [CltLayerController::class, 'index'])->name('layers.index');

    // Import / Export (Global)
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('/', [ImportController::class, 'index'])->name('index');
        Route::post('/', [ImportController::class, 'store'])->name('store');
        Route::post('/resolve', [ImportController::class, 'resolve'])->name('resolve');
    });

    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/', [ExportController::class, 'index'])->name('index');
        Route::get('/{supplier}', [ExportController::class, 'download'])->name('download');
    });

    // Activity logs — read only
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

});
