<?php

namespace Tests\Feature\Profile;

use App\Models\AlumniProfile;
use App\Models\MasterMajor;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_profile(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data profil berhasil diambil.',
            ]);
    }

    public function test_view_profile_returns_404_if_incomplete(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Profil tidak ditemukan atau belum lengkap.',
            ]);
    }

    public function test_user_can_create_and_update_profile(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        $major = MasterMajor::factory()->create(['school_id' => $school->id]);
        Sanctum::actingAs($user);

        $payload = [
            'major_id' => $major->id,
            'graduation_year' => 2024,
            'phone_number' => '08123456789',
            'linkedin_url' => 'https://linkedin.com/in/budi',
        ];

        $responseCreate = $this->postJson('/api/v1/profile', $payload);
        $responseCreate->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Profil berhasil dibuat.']);

        $this->assertDatabaseHas('alumni_profiles', [
            'user_id' => $user->id,
            'phone_number' => '08123456789',
        ]);

        $payloadUpdate = array_merge($payload, ['phone_number' => '08999999999']);
        $responseUpdate = $this->postJson('/api/v1/profile', $payloadUpdate);
        $responseUpdate->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Profil berhasil diperbarui.']);

        $this->assertDatabaseHas('alumni_profiles', [
            'user_id' => $user->id,
            'phone_number' => '08999999999',
        ]);
    }
}
