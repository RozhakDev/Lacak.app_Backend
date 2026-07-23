<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\HealthCheckController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\TracerController;
use App\Http\Controllers\Api\V1\MasterController;
use App\Http\Controllers\Api\V1\JobVacancyController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\AlumniExperienceController;

// Health Check
Route::get('/health', [HealthCheckController::class, 'index']);

// Fallback Route for unauthenticated API requests
Route::get('/login', function () {
    return response()->json([
        'success' => false,
        'message' => 'Sesi tidak valid. Silakan login kembali.'
    ], 401);
})->name('login');

// V1 API Group
Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::middleware('throttle:5,1,login')->post('/login', [AuthController::class, 'login']);
        Route::middleware('throttle:3,1,forgot-password')->post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::middleware('throttle:3,1,resend-otp')->post('/resend-otp', [AuthController::class, 'resendOtp']);

    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::post('/', [ProfileController::class, 'update']);
            
            Route::prefix('experiences')->group(function () {
                Route::post('/', [AlumniExperienceController::class, 'store']);
                Route::put('/{id}', [AlumniExperienceController::class, 'update']);
                Route::delete('/{id}', [AlumniExperienceController::class, 'destroy']);
            });
        });

        Route::prefix('tracer')->group(function () {
            Route::post('/submissions', [TracerController::class, 'store']);
        });

        Route::prefix('jobs')->group(function () {
            Route::get('/applications', [JobVacancyController::class, 'myApplications']);
            Route::get('/', [JobVacancyController::class, 'index']);
            Route::post('/{id}/apply', [JobVacancyController::class, 'apply']);
            Route::get('/{id}', [JobVacancyController::class, 'show']);
        });

        Route::prefix('events')->group(function () {
            Route::get('/my-events', [EventController::class, 'myEvents']);
            Route::get('/', [EventController::class, 'index']);
            Route::post('/{id}/register', [EventController::class, 'register']);
            Route::get('/{id}', [EventController::class, 'show']);
        });
    });

    Route::prefix('master')->group(function () {
        Route::get('/majors', [MasterController::class, 'getMajors']);
        Route::get('/tracer-options', [MasterController::class, 'getTracerOptions']);
    });
});
