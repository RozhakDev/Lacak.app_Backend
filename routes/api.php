<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HealthCheckController;

Route::get('/health', [HealthCheckController::class, 'index']);