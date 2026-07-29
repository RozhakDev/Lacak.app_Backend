<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->ajax() || $request->wantsJson() || $request->is('*update-column*'),
        );

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, Request $request) {
            if ($request->ajax() || $request->wantsJson() || $request->is('*update-column*')) {
                return response()->json([
                    'message' => 'Akses ditolak: Anda tidak berhak memodifikasi data milik pusat/sekolah lain.',
                    'messageType' => 'error'
                ], 403);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->ajax() || $request->wantsJson() || $request->is('*update-column*')) {
                return response()->json([
                    'message' => 'Akses ditolak: Anda tidak berhak memodifikasi data milik pusat/sekolah lain.',
                    'messageType' => 'error'
                ], 403);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi tidak valid. Silakan login kembali.'
                ], 401);
            }
        });
    })->create();
