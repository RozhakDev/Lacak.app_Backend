<?php

namespace Tests\Unit\Policies;

use App\Models\JobVacancy;
use App\Models\School;
use App\Models\User;
use App\Policies\JobVacancyPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobVacancyPolicyTest extends TestCase
{
    use RefreshDatabase;

    private JobVacancyPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->policy = new JobVacancyPolicy();
    }

    public function test_super_admin_can_create_job_vacancy(): void
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $this->assertTrue($this->policy->create($superAdmin));
    }

    public function test_super_admin_can_update_any_job_vacancy(): void
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $school = School::factory()->create();
        $job = JobVacancy::factory()->create(['school_id' => $school->id]);

        $this->assertTrue($this->policy->update($superAdmin, $job));
    }

    public function test_super_admin_can_delete_any_job_vacancy(): void
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $school = School::factory()->create();
        $job = JobVacancy::factory()->create(['school_id' => $school->id]);

        $this->assertTrue($this->policy->delete($superAdmin, $job));
    }

    public function test_super_admin_cannot_force_delete(): void
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $school = School::factory()->create();
        $job = JobVacancy::factory()->create(['school_id' => $school->id]);

        $this->assertFalse($this->policy->forceDelete($superAdmin, $job));
    }

    public function test_admin_bkk_can_create_job_vacancy(): void
    {
        $school = School::factory()->create();
        $adminBkk = User::factory()->create(['school_id' => $school->id]);
        $adminBkk->assignRole('Admin BKK');

        $this->assertTrue($this->policy->create($adminBkk));
    }

    public function test_admin_bkk_can_update_own_school_job_vacancy(): void
    {
        $school = School::factory()->create();
        $adminBkk = User::factory()->create(['school_id' => $school->id]);
        $adminBkk->assignRole('Admin BKK');

        $job = JobVacancy::factory()->create([
            'school_id' => $school->id,
            'created_by' => $adminBkk->id,
        ]);

        $this->assertTrue($this->policy->update($adminBkk, $job));
    }

    public function test_admin_bkk_cannot_update_other_school_job_vacancy(): void
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        $adminBkk = User::factory()->create(['school_id' => $school1->id]);
        $adminBkk->assignRole('Admin BKK');

        $job = JobVacancy::factory()->create(['school_id' => $school2->id]);

        $this->assertFalse($this->policy->update($adminBkk, $job));
    }

    public function test_admin_bkk_cannot_update_job_created_by_super_admin(): void
    {
        $school = School::factory()->create();
        $adminBkk = User::factory()->create(['school_id' => $school->id]);
        $adminBkk->assignRole('Admin BKK');

        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $job = JobVacancy::factory()->create([
            'school_id' => $school->id,
            'created_by' => $superAdmin->id,
        ]);

        $this->assertFalse($this->policy->update($adminBkk, $job));
    }

    public function test_admin_bkk_can_delete_own_school_job_vacancy(): void
    {
        $school = School::factory()->create();
        $adminBkk = User::factory()->create(['school_id' => $school->id]);
        $adminBkk->assignRole('Admin BKK');

        $job = JobVacancy::factory()->create([
            'school_id' => $school->id,
            'created_by' => $adminBkk->id,
        ]);

        $this->assertTrue($this->policy->delete($adminBkk, $job));
    }

    public function test_admin_bkk_cannot_delete_other_school_job_vacancy(): void
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        $adminBkk = User::factory()->create(['school_id' => $school1->id]);
        $adminBkk->assignRole('Admin BKK');

        $job = JobVacancy::factory()->create(['school_id' => $school2->id]);

        $this->assertFalse($this->policy->delete($adminBkk, $job));
    }

    public function test_regular_user_cannot_create_job_vacancy(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        $user->assignRole('User');

        $this->assertFalse($this->policy->create($user));
    }

    public function test_regular_user_cannot_update_job_vacancy(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        $user->assignRole('User');

        $job = JobVacancy::factory()->create(['school_id' => $school->id]);

        $this->assertFalse($this->policy->update($user, $job));
    }
}
