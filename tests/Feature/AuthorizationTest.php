<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_access_to_all_protected_routes_returns_401(): void
    {
        $protectedRoutes = [
            ['POST', '/api/v1/auth/logout'],
            ['GET', '/api/v1/profile'],
            ['POST', '/api/v1/profile'],
            ['GET', '/api/v1/profile/experiences'],
            ['POST', '/api/v1/profile/experiences'],
            ['PUT', '/api/v1/profile/experiences/1'],
            ['DELETE', '/api/v1/profile/experiences/1'],
            ['GET', '/api/v1/tracer/submissions'],
            ['POST', '/api/v1/tracer/submissions'],
            ['GET', '/api/v1/jobs'],
            ['GET', '/api/v1/jobs/1'],
            ['GET', '/api/v1/jobs/applications'],
            ['POST', '/api/v1/jobs/1/apply'],
            ['GET', '/api/v1/events'],
            ['GET', '/api/v1/events/1'],
            ['GET', '/api/v1/events/my-events'],
            ['POST', '/api/v1/events/1/register'],
        ];

        foreach ($protectedRoutes as [$method, $uri]) {
            $response = $this->json($method, $uri);
            $this->assertEquals(
                401,
                $response->status(),
                "Route [{$method} {$uri}] harus mengembalikan 401 tanpa autentikasi."
            );
        }
    }

    public function test_public_routes_are_accessible_without_authentication(): void
    {
        $publicRoutes = [
            ['GET', '/api/v1/master/schools'],
            ['GET', '/api/v1/master/majors'],
            ['GET', '/api/v1/master/tracer-options'],
        ];

        foreach ($publicRoutes as [$method, $uri]) {
            $response = $this->json($method, $uri);
            $this->assertEquals(
                200,
                $response->status(),
                "Route [{$method} {$uri}] harus bisa diakses tanpa autentikasi."
            );
        }
    }
}
