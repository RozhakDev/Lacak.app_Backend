<?php

namespace Tests\Feature\AlumniExperience;

use App\Models\AlumniExperience;
use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AlumniExperienceValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_alumni_experience_happy_path(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $payload = [
            'company_name' => 'PT Teknologi Utama',
            'position' => 'Senior Backend Developer',
            'description' => 'Mengembangkan RESTful APIs.',
            'start_date' => '2023-01-01',
            'end_date' => '2024-01-01',
            'is_current' => false,
        ];

        $response = $this->postJson('/api/v1/profile/experiences', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Pengalaman berhasil ditambahkan.',
            ]);

        $this->assertDatabaseHas('alumni_experiences', [
            'alumni_profile_id' => $profile->id,
            'company_name' => 'PT Teknologi Utama',
            'position' => 'Senior Backend Developer',
        ]);
    }

    public function test_update_alumni_experience_happy_path(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        $experience = AlumniExperience::factory()->create(['alumni_profile_id' => $profile->id]);
        Sanctum::actingAs($user);

        $payload = [
            'company_name' => 'PT Innovasi Digital',
            'position' => 'Tech Lead',
            'start_date' => '2023-05-01',
        ];

        $response = $this->putJson("/api/v1/profile/experiences/{$experience->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Pengalaman berhasil diperbarui.',
            ]);

        $this->assertDatabaseHas('alumni_experiences', [
            'id' => $experience->id,
            'company_name' => 'PT Innovasi Digital',
            'position' => 'Tech Lead',
        ]);
    }

    public function test_store_alumni_experience_same_day_start_and_end_date(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $payload = [
            'company_name' => 'PT Project One Day',
            'position' => 'Consultant',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-01',
            'is_current' => false,
        ];

        $response = $this->postJson('/api/v1/profile/experiences', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Pengalaman berhasil ditambahkan.',
            ]);

        $this->assertDatabaseHas('alumni_experiences', [
            'alumni_profile_id' => $profile->id,
            'company_name' => 'PT Project One Day',
            'start_date' => '2026-01-01 00:00:00',
            'end_date' => '2026-01-01 00:00:00',
        ]);
    }

    public function test_store_alumni_experience_validation_negative_paths(): void
    {
        $user = User::factory()->create();
        AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response1 = $this->postJson('/api/v1/profile/experiences', [
            'start_date' => '2023-01-01',
        ]);

        $response1->assertStatus(422)
            ->assertJsonValidationErrors(['company_name', 'position']);

        $response2 = $this->postJson('/api/v1/profile/experiences', [
            'company_name' => 'PT Sample',
            'position' => 'Developer',
            'start_date' => 'not-a-valid-date',
        ]);

        $response2->assertStatus(422)
            ->assertJsonValidationErrors(['start_date']);

        $response3 = $this->postJson('/api/v1/profile/experiences', [
            'company_name' => 'PT Sample',
            'position' => 'Developer',
            'start_date' => '2024-01-01',
            'end_date' => '2023-01-01',
        ]);

        $response3->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_update_alumni_experience_validation_negative_paths(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        $experience = AlumniExperience::factory()->create(['alumni_profile_id' => $profile->id]);
        Sanctum::actingAs($user);

        $response1 = $this->putJson("/api/v1/profile/experiences/{$experience->id}", [
            'company_name' => '',
        ]);

        $response1->assertStatus(422)
            ->assertJsonValidationErrors(['company_name']);

        $response2 = $this->putJson("/api/v1/profile/experiences/{$experience->id}", [
            'start_date' => 'invalid-date',
        ]);

        $response2->assertStatus(422)
            ->assertJsonValidationErrors(['start_date']);

        $response3 = $this->putJson("/api/v1/profile/experiences/{$experience->id}", [
            'start_date' => '2025-06-01',
            'end_date' => '2024-01-01',
        ]);

        $response3->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }
}
