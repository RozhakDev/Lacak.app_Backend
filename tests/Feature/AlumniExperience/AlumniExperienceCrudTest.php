<?php

namespace Tests\Feature\AlumniExperience;

use App\Models\AlumniExperience;
use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AlumniExperienceCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_own_experiences(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        AlumniExperience::factory()->count(3)->create(['alumni_profile_id' => $profile->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/profile/experiences');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_delete_own_experience(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        $experience = AlumniExperience::factory()->create(['alumni_profile_id' => $profile->id]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/profile/experiences/{$experience->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('alumni_experiences', ['id' => $experience->id]);
    }

    public function test_user_cannot_delete_another_users_experience(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $profile2 = AlumniProfile::factory()->create(['user_id' => $user2->id]);
        $experience = AlumniExperience::factory()->create(['alumni_profile_id' => $profile2->id]);
        Sanctum::actingAs($user1);

        $response = $this->deleteJson("/api/v1/profile/experiences/{$experience->id}");

        $this->assertContains($response->status(), [400, 404]);
        $this->assertDatabaseHas('alumni_experiences', ['id' => $experience->id]);
    }
}
