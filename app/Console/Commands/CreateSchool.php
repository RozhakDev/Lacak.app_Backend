<?php

namespace App\Console\Commands;

use App\Models\School;

class CreateSchool extends BaseCommand
{
    protected $signature = 'app:create-school
        {--name= : School name}
        {--code= : Unique school code (e.g. SMKN1)}
        {--email= : School email}
        {--phone= : School phone number}
        {--address= : School address}';

    protected $description = 'Register a new school into the system';

    public function handle(): int
    {
        $name = $this->resolveOption('name', 'School name');
        $code = $this->resolveOption('code', 'School code (e.g. SMKN1)');
        $email = $this->resolveOption('email', 'School email');
        $phone = $this->resolveOption('phone', 'Phone number');
        $address = $this->resolveOption('address', 'Address');

        if (!$this->validateOrFail(
            compact('name', 'code', 'email', 'phone', 'address'),
            [
                'name' => ['required', 'string', 'max:255'],
                'code' => ['required', 'string', 'max:50', 'unique:schools,code'],
                'email' => ['required', 'email', 'unique:schools,email'],
                'phone' => ['required', 'string', 'max:20'],
                'address' => ['required', 'string'],
            ]
        )) {
            return self::FAILURE;
        }

        $school = School::create([
            'name' => $name,
            'code' => $code,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'is_active' => true,
        ]);

        $this->info("School [{$school->name}] registered with ID [{$school->id}].");

        return self::SUCCESS;
    }
}
