<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "Dokumentasi REST API untuk Lacak.app Backend",
    title: "Lacak.app API Documentation"
)]
#[OA\Server(
    url: "/",
    description: "API Server (Dynamic Host)"
)]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Masukkan token Sanctum yang didapatkan dari proses Login."
)]
abstract class Controller
{
    use ApiResponse;
}
