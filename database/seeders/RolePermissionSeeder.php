<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-users',
            'manage-bkk',
            'manage-master-data',
            'view-analytic-dashboard',
            'submit-tracer-study',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $adminBkkRole = Role::firstOrCreate(['name' => 'Admin BKK']);
        $adminBkkRole->givePermissionTo([
            'manage-bkk',
            'view-analytic-dashboard'
        ]);

        $userRole = Role::firstOrCreate(['name' => 'User']);
        $userRole->givePermissionTo([
            'submit-tracer-study'
        ]);
    }
}
