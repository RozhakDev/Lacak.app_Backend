<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterMajor;

class MasterMajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = [
            ['name' => 'Teknik Komputer dan Jaringan', 'code' => 'TKJ'],
            ['name' => 'Rekayasa Perangkat Lunak', 'code' => 'RPL'],
            ['name' => 'Desain Komunikasi Visual', 'code' => 'DKV'],
            ['name' => 'Akuntansi dan Keuangan Lembaga', 'code' => 'AKL'],
            ['name' => 'Otomatisasi dan Tata Kelola Perkantoran', 'code' => 'OTKP'],
            ['name' => 'Teknik Kendaraan Ringan Otomotif', 'code' => 'TKRO'],
        ];

        foreach ($majors as $major) {
            MasterMajor::firstOrCreate(
                ['code' => $major['code']],
                ['name' => $major['name']]
            );
        }
    }
}
