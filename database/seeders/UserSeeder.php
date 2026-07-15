<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@lacak.app'],
            [
                'name' => 'Super Admin Pusat',
                'password' => Hash::make('Admin#123'),                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        $adminBkk = User::firstOrCreate(
            ['email' => 'bkk@smkbisa.sch.id'],
            [
                'name' => 'Admin BKK SMK',
                'password' => Hash::make('Admin#123'),
                'email_verified_at' => now(),
            ]
        );
        $adminBkk->assignRole('Admin BKK');
    }
}
