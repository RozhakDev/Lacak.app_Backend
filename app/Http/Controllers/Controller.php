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
    url: L5_SWAGGER_CONST_HOST,
    description: "API Server"
)]
abstract class Controller
{
    use ApiResponse;
}
