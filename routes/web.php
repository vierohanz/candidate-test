<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('start');

// Web Dashboard (Frontend)
Route::get('/dashboard', DashboardController::class)->name('dashboard');

// Note: All data management is now handled via API at /api/v1/...
