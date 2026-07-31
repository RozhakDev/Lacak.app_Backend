<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends BaseCommand
{
    protected $signature = 'app:create-super-admin
        {--name= : Full name}
        {--email= : Email address}
        {--password= : Password}';

    protected $description = 'Create a new Super Admin account';

    public function handle(): int
    {
        $name = $this->resolveOption('name', 'Name');
        $email = $this->resolveOption('email', 'Email');
        $password = $this->resolveOption('password', 'Password', secret: true);

        if (!$this->validateOrFail(compact('name', 'email', 'password'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ])) {
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'school_id' => null,
            'nisn' => null,
        ]);

        $user->assignRole('Super Admin');

        $this->info("Super Admin account [{$email}] created successfully.");

        return self::SUCCESS;
    }
}
