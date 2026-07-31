<?php

namespace App\Console\Commands;

use App\Models\MasterMajor;
use App\Models\School;

class CreateMajor extends BaseCommand
{
    protected $signature = 'app:create-major
        {--name= : Major name}
        {--code= : Major code (e.g. RPL)}
        {--school-id= : School ID}';

    protected $description = 'Add a new major to a school';

    public function handle(): int
    {
        $schools = School::where('is_active', true)->get();

        if ($schools->isEmpty()) {
            $this->error('No active schools found. Please run app:create-school first.');
            return self::FAILURE;
        }

        $name = $this->resolveOption('name', 'Major name');
        $code = $this->resolveOption('code', 'Major code (e.g. RPL)');

        $schoolId = $this->option('school-id');

        if (!$schoolId) {
            $this->table(['ID', 'Name', 'Code'], $schools->map(fn($s) => [$s->id, $s->name, $s->code])->toArray());
            $schoolId = $this->ask('School ID');
        }

        $school = School::find($schoolId);

        $fullCode = $school ? $school->code . '-' . strtoupper($code) : $code;

        if (!$this->validateOrFail(
            ['name' => $name, 'code' => $fullCode, 'school_id' => $schoolId],
            [
                'name' => ['required', 'string', 'max:255'],
                'code' => ['required', 'string', 'unique:master_majors,code'],
                'school_id' => ['required', 'exists:schools,id'],
            ]
        )) {
            return self::FAILURE;
        }

        $major = MasterMajor::create([
            'name' => $name,
            'code' => $fullCode,
            'school_id' => $schoolId,
        ]);

        $this->info("Major [{$major->name}] with code [{$major->code}] added to school [{$school->name}].");

        return self::SUCCESS;
    }
}
