<?php

namespace Tests\Feature\Event;

use App\Models\AlumniProfile;
use App\Models\Event;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_alumni_profile_cannot_register_event(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        Sanctum::actingAs($user);

        $event = Event::factory()->create([
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/v1/events/{$event->id}/register");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Silakan lengkapi profil alumni Anda terlebih dahulu sebelum mendaftar kegiatan.',
            ]);
    }

    public function test_user_with_profile_can_register_active_event(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $event = Event::factory()->create([
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/v1/events/{$event->id}/register");

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Berhasil mendaftar kegiatan.',
            ]);

        $this->assertDatabaseHas('event_participants', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'registered',
        ]);
    }

    public function test_user_cannot_register_twice_for_the_same_event(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $event = Event::factory()->create([
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $this->postJson("/api/v1/events/{$event->id}/register");

        $response = $this->postJson("/api/v1/events/{$event->id}/register");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Anda sudah terdaftar di kegiatan ini.',
            ]);
    }

    public function test_user_cannot_register_for_inactive_event(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $event = Event::factory()->create([
            'school_id' => $school->id,
            'is_active' => false,
        ]);

        $response = $this->postJson("/api/v1/events/{$event->id}/register");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Event/Kegiatan tidak ditemukan atau sudah tidak aktif.',
            ]);
    }
}
