<?php

namespace Tests\Unit\Policies;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\School;
use App\Models\User;
use App\Policies\EventParticipantPolicy;
use App\Policies\JobApplicationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationParticipantPolicyTest extends TestCase
{
    use RefreshDatabase;

    private EventParticipantPolicy $eventParticipantPolicy;
    private JobApplicationPolicy $jobApplicationPolicy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->eventParticipantPolicy = new EventParticipantPolicy();
        $this->jobApplicationPolicy   = new JobApplicationPolicy();
    }

    public function test_nobody_can_create_event_participant_directly(): void
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $school = School::factory()->create();
        $adminBkk = User::factory()->create(['school_id' => $school->id]);
        $adminBkk->assignRole('Admin BKK');

        $this->assertFalse($this->eventParticipantPolicy->create($superAdmin));
        $this->assertFalse($this->eventParticipantPolicy->create($adminBkk));
    }

    public function test_super_admin_can_update_any_event_participant(): void
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $school = School::factory()->create();
        $event = Event::factory()->create(['school_id' => $school->id]);
        $participant = EventParticipant::factory()->create(['event_id' => $event->id]);

        $this->assertTrue($this->eventParticipantPolicy->update($superAdmin, $participant));
    }

    public function test_admin_bkk_can_update_participant_of_own_school_event(): void
    {
        $school = School::factory()->create();
        $adminBkk = User::factory()->create(['school_id' => $school->id]);
        $adminBkk->assignRole('Admin BKK');

        $event = Event::factory()->create([
            'school_id' => $school->id,
            'created_by' => $adminBkk->id,
        ]);
        $participant = EventParticipant::factory()->create(['event_id' => $event->id]);

        $this->assertTrue($this->eventParticipantPolicy->update($adminBkk, $participant));
    }

    public function test_admin_bkk_cannot_update_participant_of_other_school_event(): void
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        $adminBkk = User::factory()->create(['school_id' => $school1->id]);
        $adminBkk->assignRole('Admin BKK');

        $event = Event::factory()->create(['school_id' => $school2->id]);
        $participant = EventParticipant::factory()->create(['event_id' => $event->id]);

        $this->assertFalse($this->eventParticipantPolicy->update($adminBkk, $participant));
    }

    public function test_nobody_can_force_delete_event_participant(): void
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $event = Event::factory()->create();
        $participant = EventParticipant::factory()->create(['event_id' => $event->id]);

        $this->assertFalse($this->eventParticipantPolicy->forceDelete($superAdmin, $participant));
    }

    public function test_nobody_can_create_job_application_from_admin(): void
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $school = School::factory()->create();
        $adminBkk = User::factory()->create(['school_id' => $school->id]);
        $adminBkk->assignRole('Admin BKK');

        $this->assertFalse($this->jobApplicationPolicy->create($superAdmin));
        $this->assertFalse($this->jobApplicationPolicy->create($adminBkk));
    }

    public function test_super_admin_can_update_any_job_application(): void
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $school = School::factory()->create();
        $job = JobVacancy::factory()->create(['school_id' => $school->id]);
        $application = JobApplication::factory()->create(['job_vacancy_id' => $job->id]);

        $this->assertTrue($this->jobApplicationPolicy->update($superAdmin, $application));
    }

    public function test_admin_bkk_can_update_application_of_own_school_job(): void
    {
        $school = School::factory()->create();
        $adminBkk = User::factory()->create(['school_id' => $school->id]);
        $adminBkk->assignRole('Admin BKK');

        $job = JobVacancy::factory()->create([
            'school_id' => $school->id,
            'created_by' => $adminBkk->id,
        ]);
        $application = JobApplication::factory()->create(['job_vacancy_id' => $job->id]);

        $this->assertTrue($this->jobApplicationPolicy->update($adminBkk, $application));
    }

    public function test_admin_bkk_cannot_update_application_of_other_school_job(): void
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        $adminBkk = User::factory()->create(['school_id' => $school1->id]);
        $adminBkk->assignRole('Admin BKK');

        $job = JobVacancy::factory()->create(['school_id' => $school2->id]);
        $application = JobApplication::factory()->create(['job_vacancy_id' => $job->id]);

        $this->assertFalse($this->jobApplicationPolicy->update($adminBkk, $application));
    }

    public function test_nobody_can_force_delete_job_application(): void
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $job = JobVacancy::factory()->create();
        $application = JobApplication::factory()->create(['job_vacancy_id' => $job->id]);

        $this->assertFalse($this->jobApplicationPolicy->forceDelete($superAdmin, $application));
    }
}
