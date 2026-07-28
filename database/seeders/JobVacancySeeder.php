<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobVacancy;
use App\Models\User;
use App\Models\School;
use Carbon\Carbon;

class JobVacancySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $vacanciesData = [
            'SMKN1' => [
                [
                    'title' => 'Software Engineer (Web Developer)',
                    'company_name' => 'PT Teknologi Indonesia Jaya',
                    'description' => 'Kami mencari Web Developer yang berpengalaman dengan Laravel dan React. Bekerja secara penuh waktu (Full-time) dengan sistem WFO di Jakarta Selatan.',
                    'requirements' => "- Lulusan SMK Jurusan RPL/TKJ atau setara.\n- Menguasai PHP (Laravel) dan JavaScript (React/Vue).\n- Memiliki portfolio aplikasi web yang pernah dibuat.\n- Bersedia bekerja di bawah tekanan.",
                    'is_active' => true,
                    'expires_at' => Carbon::now()->addDays(30)->toDateString(),
                ],
                [
                    'title' => 'Junior Network Administrator',
                    'company_name' => 'PT Jaringan Global Solusindo',
                    'description' => 'Membuka lowongan untuk posisi Junior Network Admin. Tugas utama meliputi instalasi jaringan, troubleshooting perangkat mikrotik, dan pemeliharaan server.',
                    'requirements' => "- Minimal lulusan SMK jurusan TKJ.\n- Memahami konsep dasar jaringan komputer (TCP/IP, Routing, Switching).\n- Memiliki sertifikasi MikroTik (MTCNA) menjadi nilai tambah.\n- Sehat jasmani dan rohani.",
                    'is_active' => true,
                    'expires_at' => Carbon::now()->addDays(15)->toDateString(),
                ],
            ],
            'SMKN2' => [
                [
                    'title' => 'Staff Administrasi Keuangan',
                    'company_name' => 'Koperasi Sejahtera Bersama',
                    'description' => 'Mencari staff administrasi keuangan yang teliti untuk mengelola arus kas harian dan membuat laporan keuangan bulanan.',
                    'requirements' => "- Lulusan SMK Akuntansi atau Administrasi Perkantoran.\n- Mahir menggunakan Microsoft Excel dan software akuntansi dasar.\n- Jujur, teliti, dan disiplin.\n- Penempatan domisili Bandung.",
                    'is_active' => true,
                    'expires_at' => Carbon::now()->addDays(10)->toDateString(),
                ],
                [
                    'title' => 'Customer Service Representative',
                    'company_name' => 'PT Layanan Cepat',
                    'description' => 'Dibutuhkan Customer Service untuk melayani pelanggan secara online dan offline.',
                    'requirements' => "- SMK Jurusan Pemasaran / OTKP.\n- Komunikatif dan ramah.\n- Mampu menggunakan komputer dengan baik.",
                    'is_active' => true,
                    'expires_at' => Carbon::now()->addDays(20)->toDateString(),
                ],
            ],
            'SMKN3' => [
                [
                    'title' => 'Resepsionis Hotel Bintang 4',
                    'company_name' => 'Grand Palace Hotel',
                    'description' => 'Kami mengundang Anda bergabung sebagai Resepsionis Hotel.',
                    'requirements' => "- SMK Jurusan Perhotelan.\n- Berpenampilan menarik dan proporsional.\n- Menguasai Bahasa Inggris aktif.",
                    'is_active' => true,
                    'expires_at' => Carbon::now()->addDays(25)->toDateString(),
                ],
                [
                    'title' => 'Asisten Koki (Cook Helper)',
                    'company_name' => 'Restoran Nusantara Rasa',
                    'description' => 'Restoran kami mencari Cook Helper untuk mempersiapkan bahan masakan.',
                    'requirements' => "- Lulusan SMK Tata Boga / Jasa Boga.\n- Mengerti standar kebersihan dapur (HACCP).\n- Bersedia bekerja dalam sistem shift.",
                    'is_active' => false,
                    'expires_at' => Carbon::now()->subDays(5)->toDateString(),
                ],
            ]
        ];

        $schools = School::all();
        if ($schools->isEmpty()) return;

        foreach ($schools as $school) {
            $bkk = User::where('school_id', $school->id)->role('Admin BKK')->first();
            $creatorId = $bkk ? $bkk->id : $adminId;

            if (isset($vacanciesData[$school->code])) {
                foreach ($vacanciesData[$school->code] as $vacancy) {
                    $vacancy['created_by'] = $creatorId;
                    $vacancy['school_id'] = $school->id;
                    JobVacancy::create($vacancy);
                }
            }
        }
    }
}
