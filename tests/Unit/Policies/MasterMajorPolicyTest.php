<?php

namespace Tests\Unit\Policies;

use App\Models\MasterMajor;
use App\Models\School;
use App\Models\User;
use App\Policies\MasterMajorPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterMajorPolicyTest extends TestCase
{
    use RefreshDatabase;

    private MasterMajorPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->policy = new MasterMajorPolicy();
    }

    public function test_super_admin_has_full_access_to_master_major(): void
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $school = School::factory()->create();
        $major = MasterMajor::factory()->create(['school_id' => $school->id]);

        $this->assertTrue($this->policy->viewAny($superAdmin));
        $this->assertTrue($this->policy->view($superAdmin, $major));
        $this->assertTrue($this->policy->create($superAdmin));
        $this->assertTrue($this->policy->update($superAdmin, $major));
        $this->assertTrue($this->policy->delete($superAdmin, $major));
    }

    public function test_admin_bkk_can_view_and_manage_own_school_major(): void
    {
        $school = School::factory()->create();
        $adminBkk = User::factory()->create(['school_id' => $school->id]);
        $adminBkk->assignRole('Admin BKK');

        $major = MasterMajor::factory()->create(['school_id' => $school->id]);

        $this->assertTrue($this->policy->viewAny($adminBkk));
        $this->assertTrue($this->policy->view($adminBkk, $major));
        $this->assertTrue($this->policy->create($adminBkk));
        $this->assertTrue($this->policy->update($adminBkk, $major));
        $this->assertTrue($this->policy->delete($adminBkk, $major));
    }

    public function test_admin_bkk_cannot_manage_other_school_major(): void
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        $adminBkk = User::factory()->create(['school_id' => $school1->id]);
        $adminBkk->assignRole('Admin BKK');

        $majorOfOtherSchool = MasterMajor::factory()->create(['school_id' => $school2->id]);

        $this->assertFalse($this->policy->update($adminBkk, $majorOfOtherSchool));
        $this->assertFalse($this->policy->delete($adminBkk, $majorOfOtherSchool));
    }

    public function test_regular_user_cannot_manage_master_major(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        $user->assignRole('User');

        $major = MasterMajor::factory()->create(['school_id' => $school->id]);

        $this->assertFalse($this->policy->create($user));
        $this->assertFalse($this->policy->update($user, $major));
        $this->assertFalse($this->policy->delete($user, $major));
    }
}
