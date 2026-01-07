<?php

use Illuminate\Support\Facades\Route;

use App\Addons\HostyStats\Controllers\CategoryController;
use App\Addons\HostyStats\Controllers\MonitorController;
use App\Addons\HostyStats\Controllers\MaintenanceController;


Route::middleware(['web', 'auth:admin'])
  ->prefix('admin')
  ->group(function () {

    Route::prefix('hostystats')->name('admin.hostystats.')->group(function () {

      Route::get('/', fn () => redirect()->route('admin.hostystats.dashboard'))->name('root');

      Route::get('/dashboard', [\App\Addons\HostyStats\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

      // Categories
      Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
      Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
      Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
      Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
      Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
      Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

      // Monitors
      Route::get('/monitors', [MonitorController::class, 'index'])->name('monitors.index');
      Route::get('/monitors/create', [MonitorController::class, 'create'])->name('monitors.create');
      Route::post('/monitors', [MonitorController::class, 'store'])->name('monitors.store');
      Route::get('/monitors/{monitor}', [MonitorController::class, 'show'])->name('monitors.show');
      Route::get('/monitors/{monitor}/edit', [MonitorController::class, 'edit'])->name('monitors.edit');
      Route::put('/monitors/{monitor}', [MonitorController::class, 'update'])->name('monitors.update');
      Route::delete('/monitors/{monitor}', [MonitorController::class, 'destroy'])->name('monitors.destroy');

      // Maintenance
      Route::get('/maintenance', [MaintenanceController::class, 'edit'])
        ->name('maintenance');
      Route::post('/maintenance', [MaintenanceController::class, 'update'])
        ->name('maintenance.update');
    });
  });
