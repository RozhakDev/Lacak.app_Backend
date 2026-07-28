<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterMajor;
use App\Models\School;

class MasterMajorSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::all();
        if ($schools->isEmpty()) return;

        $schoolMajors = [
            'SMKN1' => [
                ['name' => 'Rekayasa Perangkat Lunak', 'code' => 'RPL'],
                ['name' => 'Teknik Komputer dan Jaringan', 'code' => 'TKJ'],
                ['name' => 'Desain Komunikasi Visual', 'code' => 'DKV'],
            ],
            'SMKN2' => [
                ['name' => 'Akuntansi dan Keuangan Lembaga', 'code' => 'AKL'],
                ['name' => 'Otomatisasi dan Tata Kelola Perkantoran', 'code' => 'OTKP'],
                ['name' => 'Bisnis Daring dan Pemasaran', 'code' => 'BDP'],
            ],
            'SMKN3' => [
                ['name' => 'Perhotelan', 'code' => 'PH'],
                ['name' => 'Tata Boga', 'code' => 'TB'],
                ['name' => 'Usaha Perjalanan Wisata', 'code' => 'UPW'],
            ]
        ];

        foreach ($schools as $school) {
            if (isset($schoolMajors[$school->code])) {
                foreach ($schoolMajors[$school->code] as $major) {
                    MasterMajor::firstOrCreate(
                        ['code' => $school->code . '-' . $major['code']],
                        ['name' => $major['name'], 'school_id' => $school->id]
                    );
                }
            }
        }
    }
}
