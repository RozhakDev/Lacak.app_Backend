<?php

namespace Tests\Feature\Tracer;

use App\Models\AlumniProfile;
use App\Models\TracerSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TracerSubmissionViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_tracer_submission(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        TracerSubmission::factory()->create([
            'alumni_profile_id' => $profile->id,
            'status' => 'bekerja',
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/tracer/submissions');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_user_without_submission_gets_appropriate_response(): void
    {
        $user = User::factory()->create();
        AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/tracer/submissions');

        $this->assertContains($response->status(), [200, 404]);
    }

    public function test_user_without_profile_cannot_view_tracer_submission(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/tracer/submissions');

        $response->assertStatus(403);
    }
}
