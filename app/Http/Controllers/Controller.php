<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Lacak.app API Documentation",
    description: "Dokumentasi resmi REST API Lacak.app untuk pengelolaan data Tracer Study, Bursa Kerja Khusus (BKK), dan Layanan Alumni SMK."
)]
#[OA\Server(
    url: "/",
    description: "Production / Active Host Server"
)]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Masukkan Token Sanctum Anda (Contoh: `1|AbCdEfGhIjK...`). Dapatkan token melalui endpoint `POST /api/v1/auth/login`."
)]
abstract class Controller
{
    use ApiResponse;
}
