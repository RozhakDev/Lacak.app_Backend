<?php

namespace App\Console\Commands;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncRolesPermissions extends BaseCommand
{
    protected $signature = 'app:sync-roles-permissions';

    protected $description = 'Synchronize roles and permissions to the latest system definition';

    private array $permissions = [
        'manage-users',
        'manage-bkk',
        'manage-master-data',
        'view-analytic-dashboard',
        'submit-tracer-study',
    ];

    private array $roles = [
        'Super Admin' => null,
        'Admin BKK' => ['manage-bkk', 'view-analytic-dashboard'],
        'User' => ['submit-tracer-study'],
    ];

    public function handle(): int
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach ($this->roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($rolePermissions === null) {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($rolePermissions);
            }

            $this->line("  - Role [{$roleName}] synced.");
        }

        $this->info('Roles and permissions synced successfully.');

        return self::SUCCESS;
    }
}
