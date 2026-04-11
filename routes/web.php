<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('start');

Route::get('/dashboard', DashboardController::class)->name('dashboard');

Route::view('/suppliers', 'suppliers.index')->name('suppliers.page');
Route::view('/layups', 'layups.index')->name('layups.page');
Route::view('/layers', 'layers.index')->name('layers.page');

Route::view('/imports', 'imports.index')->name('imports.index');
Route::view('/exports', 'exports.index')->name('exports.index');
Route::view('/activity-logs', 'activity-logs.index')->name('activity.index');
