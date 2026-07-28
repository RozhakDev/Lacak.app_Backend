<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $schools = [
            ['name' => 'SMKN 1 Teknologi', 'code' => 'SMKN1', 'email' => 'info@smkn1.sch.id', 'phone' => '021-111111', 'address' => 'Jl. Teknologi No.1'],
            ['name' => 'SMKN 2 Bisnis', 'code' => 'SMKN2', 'email' => 'info@smkn2.sch.id', 'phone' => '021-222222', 'address' => 'Jl. Bisnis No.2'],
            ['name' => 'SMKN 3 Pariwisata', 'code' => 'SMKN3', 'email' => 'info@smkn3.sch.id', 'phone' => '021-333333', 'address' => 'Jl. Pariwisata No.3'],
        ];

        foreach ($schools as $school) {
            School::create($school);
        }
    }
}
