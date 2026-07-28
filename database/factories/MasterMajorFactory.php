<?php

namespace Database\Factories;

use App\Models\MasterMajor;
use Illuminate\Database\Eloquent\Factories\Factory;

class MasterMajorFactory extends Factory
{
    protected $model = MasterMajor::class;

    public function definition(): array
    {
        return [
            'school_id' => \App\Models\School::factory(),
            'name' => fake()->words(2, true),
            'code' => strtoupper(fake()->unique()->lexify('???')),
        ];
    }
}
