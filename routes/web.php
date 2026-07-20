<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api/health');
});

Route::get('/admin/export/tracer', [\App\Http\Controllers\Api\V1\ExportTracerController::class, 'export'])
    ->name('export.tracer.csv')
    ->middleware(['web', \MoonShine\Laravel\Http\Middleware\Authenticate::class]);
