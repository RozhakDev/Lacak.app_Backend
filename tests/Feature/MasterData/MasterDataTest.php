<?php

namespace Tests\Feature\MasterData;

use App\Models\MasterMajor;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_fetch_active_schools(): void
    {
        School::factory()->create(['name' => 'SMKN 1 Aktif', 'is_active' => true]);
        School::factory()->create(['name' => 'SMKN 2 Pasif', 'is_active' => false]);

        $response = $this->getJson('/api/v1/master/schools');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment(['name' => 'SMKN 1 Aktif'])
            ->assertJsonMissing(['name' => 'SMKN 2 Pasif']);
    }

    public function test_anyone_can_fetch_majors_by_school_id(): void
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        MasterMajor::factory()->create(['school_id' => $school1->id, 'name' => 'Jurusan S1']);
        MasterMajor::factory()->create(['school_id' => $school2->id, 'name' => 'Jurusan S2']);

        $response = $this->getJson("/api/v1/master/majors?school_id={$school1->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Jurusan S1'])
            ->assertJsonMissing(['name' => 'Jurusan S2']);
    }

    public function test_anyone_can_fetch_tracer_options(): void
    {
        $response = $this->getJson('/api/v1/master/tracer-options');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'statuses',
                    'forms' => [
                        'bekerja' => ['location_scale', 'location_country', 'salary_range', 'is_linear'],
                        'kuliah' => ['is_linear'],
                        'wirausaha' => ['ownership_type', 'monthly_omset_range']
                    ]
                ],
            ]);
    }
}
