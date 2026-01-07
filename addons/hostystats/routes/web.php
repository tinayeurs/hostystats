<?php

use Illuminate\Support\Facades\Route;
use App\Addons\HostyStats\Controllers\Client\StatusController;

Route::middleware(['web'])
    ->get('/status', [StatusController::class, 'index'])
    ->name('hostystats.status');
