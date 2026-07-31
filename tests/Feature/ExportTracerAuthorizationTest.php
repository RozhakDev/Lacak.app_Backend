<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExportTracerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_unauthenticated_user_cannot_export_tracer(): void
    {
        $response = $this->get('/admin/export/tracer');

        $this->assertContains($response->status(), [302, 401, 403]);
    }

    public function test_regular_user_with_sanctum_token_cannot_export_tracer(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        $user->assignRole('User');
        Sanctum::actingAs($user);

        $response = $this->get('/admin/export/tracer');

        $this->assertNotEquals(200, $response->status());
    }
}
