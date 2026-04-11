<?php

use App\Http\Controllers\CltLayerController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Redirect home to dashboard or keep as welcome
Route::get('/', function () {
    return view('welcome');
});

// REMOVED 'auth' middleware from all routes
Route::get('/dashboard', DashboardController::class)->name('dashboard');

// CLT Management - ALL OPEN
Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
Route::get('/layups', [CltLayupController::class, 'index'])->name('layups.index');
Route::get('/layers', [CltLayerController::class, 'index'])->name('layers.index');

// Tools - ALL OPEN
Route::get('/exports', function () {
    return view('exports.index');
})->name('exports.index');
Route::get('/imports', function () {
    return view('imports.index');
})->name('imports.index');
Route::get('/activity', function () {
    return view('activity.index');
})->name('activity.index');

// Profile (Optional, kept but without auth)
Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// require __DIR__.'/auth.php'; // Fully disabled
