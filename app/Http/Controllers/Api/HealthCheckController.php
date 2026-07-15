<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class HealthCheckController extends Controller
{
    #[OA\Get(
        path: "/api/health",
        summary: "Pemeriksaan Kesehatan API",
        description: "Mengembalikan status operasional API Lacak.app beserta informasi runtime yang digunakan untuk pemantauan layanan, diagnosis sistem, dan verifikasi ketersediaan API.",
        tags: ["System"]
    )]
    #[OA\Response(
        response: 200,
        description: "API berhasil berjalan dan siap menerima permintaan."
    )]
    public function index(): JsonResponse
    {
        return $this->successResponse('Lacak.app API is up and running!', [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
        ]);
    }
}
