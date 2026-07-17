<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobVacancy;
use App\Models\User;
use Carbon\Carbon;

class JobVacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $vacancies = [
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
            [
                'title' => 'Graphic Designer & Video Editor',
                'company_name' => 'Creative Studio Agency',
                'description' => 'Dibutuhkan segera Graphic Designer yang kreatif dan mampu melakukan editing video untuk keperluan konten media sosial dan promosi perusahaan.',
                'requirements' => "- Pendidikan SMK jurusan Desain Komunikasi Visual (DKV).\n- Menguasai Adobe Photoshop, Illustrator, dan Premiere Pro.\n- Kreatif, inovatif, dan mampu bekerja dengan target.\n- Melampirkan link portfolio karya.",
                'is_active' => true,
                'expires_at' => Carbon::now()->addDays(45)->toDateString(),
            ],
            [
                'title' => 'Staff Administrasi Keuangan',
                'company_name' => 'Koperasi Sejahtera Bersama',
                'description' => 'Mencari staff administrasi keuangan yang teliti untuk mengelola arus kas harian dan membuat laporan keuangan bulanan.',
                'requirements' => "- Lulusan SMK Akuntansi atau Administrasi Perkantoran.\n- Mahir menggunakan Microsoft Excel dan software akuntansi dasar.\n- Jujur, teliti, dan disiplin.\n- Penempatan domisili Bandung.",
                'is_active' => true,
                'expires_at' => Carbon::now()->addDays(10)->toDateString(),
            ],
            [
                'title' => 'Teknisi Mekanik Otomotif',
                'company_name' => 'Auto Jaya Motorindo',
                'description' => 'Bengkel resmi membutuhkan mekanik terampil untuk servis berkala kendaraan roda empat (mobil) dan turun mesin.',
                'requirements' => "- SMK Jurusan Teknik Kendaraan Ringan Otomotif (TKRO).\n- Pengalaman magang / PKL di bengkel mobil diutamakan.\n- Memahami kelistrikan dasar mobil.\n- Bersedia lembur jika diperlukan.",
                'is_active' => false,
                'expires_at' => Carbon::now()->subDays(5)->toDateString(),
            ]
        ];

        foreach ($vacancies as $vacancy) {
            $vacancy['created_by'] = $adminId;
            JobVacancy::create($vacancy);
        }
    }
}
