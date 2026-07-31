<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminBkk extends BaseCommand
{
    protected $signature = 'app:create-admin-bkk
        {--name= : Full name}
        {--email= : Email address}
        {--password= : Password}
        {--school-id= : School ID}';

    protected $description = 'Create a new Admin BKK account for a school';

    public function handle(): int
    {
        $schools = School::where('is_active', true)->get();

        if ($schools->isEmpty()) {
            $this->error('No active schools found. Please run app:create-school first.');
            return self::FAILURE;
        }

        $name = $this->resolveOption('name', 'Name');
        $email = $this->resolveOption('email', 'Email');
        $password = $this->resolveOption('password', 'Password', secret: true);

        $schoolId = $this->option('school-id');

        if (!$schoolId) {
            $this->table(['ID', 'Name', 'Code'], $schools->map(fn($s) => [$s->id, $s->name, $s->code])->toArray());
            $schoolId = $this->ask('School ID');
        }

        if (!$this->validateOrFail(
            ['name' => $name, 'email' => $email, 'password' => $password, 'school_id' => $schoolId],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
                'school_id' => ['required', 'exists:schools,id'],
            ]
        )) {
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'school_id' => $schoolId,
            'nisn' => null,
        ]);

        $user->assignRole('Admin BKK');

        $school = School::find($schoolId);

        $this->info("Admin BKK account [{$email}] for school [{$school->name}] created successfully.");

        return self::SUCCESS;
    }
}
