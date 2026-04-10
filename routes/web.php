<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('start');

Route::get('/dashboard', DashboardController::class)->name('dashboard');

// Suppliers — full CRUD
Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

// CLT Layups — scoped under supplier
Route::resource('layups', \App\Http\Controllers\CltLayupController::class);

// CLT Layers — scoped under layup
Route::resource('layers', \App\Http\Controllers\CltLayerController::class);

// Import / Export
Route::prefix('import')->name('import.')->group(function () {
    Route::get('/',  [\App\Http\Controllers\ImportController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\ImportController::class, 'store'])->name('store');
    Route::post('/resolve', [\App\Http\Controllers\ImportController::class, 'resolve'])->name('resolve');
});

Route::prefix('export')->name('export.')->group(function () {
    Route::get('/',                    [\App\Http\Controllers\ExportController::class, 'index'])->name('index');
    Route::get('/{supplier}',          [\App\Http\Controllers\ExportController::class, 'download'])->name('download');
});

// Activity logs — read only
Route::get('/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
