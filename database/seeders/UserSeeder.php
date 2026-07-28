<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@lacak.app'],
            [
                'name' => 'Super Admin Pusat',
                'password' => Hash::make('Admin#123'),
                'email_verified_at' => now(),
                'school_id' => null,
            ]
        );
        $superAdmin->assignRole('Super Admin');

        $schools = \App\Models\School::all();

        foreach ($schools as $index => $school) {
            $adminBkk = User::firstOrCreate(
                ['email' => 'bkk' . ($index + 1) . '@smk.sch.id'],
                [
                    'name' => 'Admin BKK ' . $school->name,
                    'password' => Hash::make('Admin#123'),
                    'email_verified_at' => now(),
                    'school_id' => $school->id,
                ]
            );
            $adminBkk->assignRole('Admin BKK');
        }
    }
}
