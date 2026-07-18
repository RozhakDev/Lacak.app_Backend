<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\HealthCheckController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\TracerController;
use App\Http\Controllers\Api\V1\MasterController;
use App\Http\Controllers\Api\V1\JobVacancyController;

// Health Check
Route::get('/health', [HealthCheckController::class, 'index']);

// V1 API Group
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::put('/', [ProfileController::class, 'update']);
        });

        Route::prefix('tracer')->group(function () {
            Route::post('/submissions', [TracerController::class, 'store']);
        });

        Route::prefix('jobs')->group(function () {
            Route::get('/', [JobVacancyController::class, 'index']);
            Route::get('/{id}', [JobVacancyController::class, 'show']);
        });
    });

    Route::prefix('master')->group(function () {
        Route::get('/majors', [MasterController::class, 'getMajors']);
        Route::get('/tracer-options', [MasterController::class, 'getTracerOptions']);
    });
});