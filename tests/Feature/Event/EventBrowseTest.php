<?php

namespace Tests\Feature\Event;

use App\Models\AlumniProfile;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventBrowseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_active_events(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        Event::factory()->count(3)->create(['school_id' => $school->id, 'is_active' => true]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/events');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Daftar kegiatan berhasil dimuat.']);
    }

    public function test_inactive_events_are_excluded_from_listing(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        $active = Event::factory()->create(['school_id' => $school->id, 'is_active' => true, 'title' => 'Event Aktif']);
        $inactive = Event::factory()->create(['school_id' => $school->id, 'is_active' => false, 'title' => 'Event Pasif']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/events');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Event Aktif'])
            ->assertJsonMissing(['title' => 'Event Pasif']);
    }

    public function test_user_can_view_active_event_detail(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        $event = Event::factory()->create(['school_id' => $school->id, 'is_active' => true]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Detail kegiatan berhasil dimuat.']);
    }

    public function test_inactive_event_detail_returns_404(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        $event = Event::factory()->create(['school_id' => $school->id, 'is_active' => false]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/events/{$event->id}");

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_user_can_view_my_registered_events(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        $event = Event::factory()->create(['school_id' => $school->id, 'is_active' => true]);

        EventParticipant::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'registered',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/events/my-events');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Daftar kegiatan yang diikuti berhasil dimuat.']);
    }
}
