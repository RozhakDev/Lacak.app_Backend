<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        return [
            'name' => 'SMKN ' . $this->faker->numberBetween(1, 100) . ' ' . $this->faker->city(),
            'code' => strtoupper($this->faker->unique()->lexify('SMK???')),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'is_active' => true,
        ];
    }
}
