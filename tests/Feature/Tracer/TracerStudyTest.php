<?php

namespace Tests\Feature\Tracer;

use App\Models\AlumniProfile;
use App\Models\TracerSubmission;
use App\Models\TracerWork;
use App\Models\TracerStudy;
use App\Models\TracerEntrepreneur;
use App\Models\User;
use App\Services\TracerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Exception;

class TracerStudyTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_alumni_profile_returns_403(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'status' => 'bekerja',
            'location_scale' => 'dalam_kota',
            'location_country' => 'dalam_negeri',
            'field_of_work' => 'Teknologi Informasi',
            'salary_range' => '5_-_10_juta',
            'company_name' => 'PT Solusi Tech',
            'position' => 'Software Engineer',
            'start_date' => '2024-01-15',
            'is_linear' => true,
        ];

        $response = $this->postJson('/api/v1/tracer/submissions', $payload);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Profil Anda belum lengkap. Silakan lengkapi profil terlebih dahulu.',
            ]);
    }

    public function test_successful_submission_for_bekerja_status(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $payload = [
            'status' => 'bekerja',
            'location_scale' => 'dalam_kota',
            'location_country' => 'dalam_negeri',
            'field_of_work' => 'Teknologi Informasi',
            'salary_range' => '5_-_10_juta',
            'company_name' => 'PT Solusi Digital',
            'position' => 'Backend Developer',
            'start_date' => '2024-01-15',
            'is_linear' => true,
        ];

        $response = $this->postJson('/api/v1/tracer/submissions', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Data Tracer Study berhasil disimpan.',
            ]);

        $this->assertDatabaseHas('tracer_submissions', [
            'alumni_profile_id' => $profile->id,
            'status' => 'bekerja',
        ]);

        $this->assertDatabaseHas('tracer_works', [
            'company_name' => 'PT Solusi Digital',
            'position' => 'Backend Developer',
        ]);
    }

    public function test_successful_submission_for_kuliah_status(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $payload = [
            'status' => 'kuliah',
            'university_name' => 'Universitas Indonesia',
            'enrollment_date' => '2024-09-01',
            'is_linear' => true,
        ];

        $response = $this->postJson('/api/v1/tracer/submissions', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Data Tracer Study berhasil disimpan.',
            ]);

        $this->assertDatabaseHas('tracer_submissions', [
            'alumni_profile_id' => $profile->id,
            'status' => 'kuliah',
        ]);

        $this->assertDatabaseHas('tracer_studies', [
            'university_name' => 'Universitas Indonesia',
        ]);
    }

    public function test_successful_submission_for_wirausaha_status(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $payload = [
            'status' => 'wirausaha',
            'ownership_type' => 'sendiri',
            'employee_count' => 5,
            'monthly_omset_range' => '5_-_15_juta',
            'business_type' => 'Kreatif Studio',
        ];

        $response = $this->postJson('/api/v1/tracer/submissions', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Data Tracer Study berhasil disimpan.',
            ]);

        $this->assertDatabaseHas('tracer_submissions', [
            'alumni_profile_id' => $profile->id,
            'status' => 'wirausaha',
        ]);

        $this->assertDatabaseHas('tracer_entrepreneurs', [
            'business_type' => 'Kreatif Studio',
            'employee_count' => 5,
        ]);
    }

    public function test_missing_required_nested_data_triggers_db_transaction_rollback(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);

        $existingSubmission = TracerSubmission::factory()->create([
            'alumni_profile_id' => $profile->id,
            'status' => 'kuliah',
        ]);

        $existingStudy = TracerStudy::factory()->create([
            'tracer_submission_id' => $existingSubmission->id,
            'university_name' => 'Universitas Indonesia',
            'enrollment_date' => '2024-09-01',
            'is_linear' => true,
        ]);

        $tracerService = app(TracerService::class);

        $invalidData = [
            'status' => 'bekerja',
            'location_scale' => 'dalam_kota',
            'location_country' => 'dalam_negeri',
            'field_of_work' => 'Teknologi Informasi',
            'salary_range' => '5_-_10_juta',
            'position' => 'Developer',
            'start_date' => '2024-01-01',
            'is_linear' => true,
        ];

        $exceptionCaught = false;
        try {
            $tracerService->submitTracer($user, $invalidData);
        } catch (\Throwable $e) {
            $exceptionCaught = true;
        }

        $this->assertTrue($exceptionCaught, 'Exception should be thrown when missing required nested data during update.');

        $this->assertDatabaseHas('tracer_submissions', [
            'id' => $existingSubmission->id,
            'status' => 'kuliah',
        ]);

        $this->assertDatabaseHas('tracer_studies', [
            'id' => $existingStudy->id,
            'university_name' => 'Universitas Indonesia',
            'deleted_at' => null,
        ]);

        $this->assertDatabaseCount('tracer_works', 0);
    }

    public function test_force_delete_on_tracer_submission_cascades_deletion_to_child_models(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);

        $submission = TracerSubmission::factory()->create([
            'alumni_profile_id' => $profile->id,
            'status' => 'bekerja',
        ]);

        $work = TracerWork::factory()->create([
            'tracer_submission_id' => $submission->id,
        ]);

        $study = TracerStudy::factory()->create([
            'tracer_submission_id' => $submission->id,
        ]);

        $entrepreneur = TracerEntrepreneur::factory()->create([
            'tracer_submission_id' => $submission->id,
        ]);

        $submissionId = $submission->id;
        $workId = $work->id;
        $studyId = $study->id;
        $entrepreneurId = $entrepreneur->id;

        $submission->forceDelete();

        $this->assertDatabaseMissing('tracer_submissions', ['id' => $submissionId]);
        $this->assertDatabaseMissing('tracer_works', ['id' => $workId]);
        $this->assertDatabaseMissing('tracer_studies', ['id' => $studyId]);
        $this->assertDatabaseMissing('tracer_entrepreneurs', ['id' => $entrepreneurId]);
    }
}
