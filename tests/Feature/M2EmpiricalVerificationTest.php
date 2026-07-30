<?php

namespace Tests\Feature;

use App\Models\AlumniExperience;
use App\Models\AlumniProfile;
use App\Models\JobVacancy;
use App\Models\School;
use App\Models\TracerSubmission;
use App\Models\TracerWork;
use App\Models\TracerStudy;
use App\Models\TracerEntrepreneur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class M2EmpiricalVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_rate_limiting_deep_verification(): void
    {
        Cache::flush();

        User::factory()->create([
            'email' => 'rate_test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $res = $this->postJson('/api/v1/auth/login', [
                'identifier' => 'rate_test@example.com',
                'password' => 'wrong-pass',
            ]);
            $this->assertEquals(401, $res->status(), "Request ke-{$i} seharusnya 401");
        }

        $res6 = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'rate_test@example.com',
            'password' => 'wrong-pass',
        ]);
        $this->assertEquals(429, $res6->status(), 'Request ke-6 wajib memicu HTTP 429 (Too Many Requests)');

        $res7 = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'rate_test@example.com',
            'password' => 'wrong-pass',
        ]);
        $this->assertEquals(429, $res7->status(), 'Request ke-7 wajib tetap 429');

        $resOther = $this->getJson('/api/v1/master/majors');
        $this->assertEquals(200, $resOther->status(), 'Rute master data wajib tetap 200, tidak terpengaruh rate limit login');
    }

    public function test_hard_delete_removes_row_permanently(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        $submission = TracerSubmission::factory()->create(['alumni_profile_id' => $profile->id]);
        $work = TracerWork::factory()->create(['tracer_submission_id' => $submission->id]);
        $workId = $work->id;

        $this->assertDatabaseHas('tracer_works', ['id' => $workId]);

        $work->delete();

        $rawCount = DB::table('tracer_works')->where('id', $workId)->count();
        $this->assertEquals(0, $rawCount, 'Record wajib langsung hilang dari DB setelah delete() (hard delete)');

        $this->assertDatabaseMissing('tracer_works', ['id' => $workId]);
    }

    public function test_job_vacancy_docx_upload_verification(): void
    {
        Storage::fake('public');

        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        $job = JobVacancy::factory()->create([
            'school_id' => $school->id,
            'is_active' => true,
            'expires_at' => now()->addDays(10),
        ]);
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create(
            'cv.docx',
            400,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );

        $response = $this->postJson("/api/v1/jobs/{$job->id}/apply", [
            'cv' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Berhasil melamar pekerjaan.',
            ]);

        $this->assertDatabaseHas('job_applications', [
            'job_vacancy_id' => $job->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_job_vacancy_file_size_boundary_5120kb_vs_5121kb_isolated(): void
    {
        Storage::fake('public');

        $school1 = School::factory()->create();
        $user1 = User::factory()->create(['school_id' => $school1->id]);
        $job1 = JobVacancy::factory()->create([
            'school_id' => $school1->id,
            'is_active' => true,
            'expires_at' => now()->addDays(10),
        ]);
        Sanctum::actingAs($user1);

        $validSizeFile = UploadedFile::fake()->create('cv_exact_5mb.pdf', 5120, 'application/pdf');
        $responseValid = $this->postJson("/api/v1/jobs/{$job1->id}/apply", [
            'cv' => $validSizeFile,
        ]);
        $responseValid->assertStatus(201)->assertJson(['success' => true]);

        $school2 = School::factory()->create();
        $user2 = User::factory()->create(['school_id' => $school2->id]);
        $job2 = JobVacancy::factory()->create([
            'school_id' => $school2->id,
            'is_active' => true,
            'expires_at' => now()->addDays(10),
        ]);
        Sanctum::actingAs($user2);

        $oversizedFile = UploadedFile::fake()->create('cv_exceed_5mb.pdf', 5121, 'application/pdf');
        $responseExceeded = $this->postJson("/api/v1/jobs/{$job2->id}/apply", [
            'cv' => $oversizedFile,
        ]);
        $responseExceeded->assertStatus(422)->assertJsonValidationErrors(['cv']);
    }

    public function test_alumni_experience_same_day_date_range_validation(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $payload = [
            'company_name' => 'PT One Day Task',
            'position' => 'Short-term Specialist',
            'start_date' => '2026-05-10',
            'end_date' => '2026-05-10',
            'is_current' => false,
        ];

        $responseStore = $this->postJson('/api/v1/profile/experiences', $payload);
        $responseStore->assertStatus(201);

        $this->assertDatabaseHas('alumni_experiences', [
            'alumni_profile_id' => $profile->id,
            'company_name' => 'PT One Day Task',
            'start_date' => '2026-05-10 00:00:00',
            'end_date' => '2026-05-10 00:00:00',
        ]);

        $experienceId = $responseStore->json('data.id');

        $responseUpdate = $this->putJson("/api/v1/profile/experiences/{$experienceId}", [
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-01',
        ]);
        $responseUpdate->assertStatus(200);

        $this->assertDatabaseHas('alumni_experiences', [
            'id' => $experienceId,
            'start_date' => '2026-08-01 00:00:00',
            'end_date' => '2026-08-01 00:00:00',
        ]);
    }
}
