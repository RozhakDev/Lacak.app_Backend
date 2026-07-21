<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $events = [
            [
                'title' => 'Reuni Akbar Lintas Angkatan 2026',
                'event_type' => 'other',
                'location_type' => 'offline',
                'location_details' => 'Gedung Serbaguna Sekolah',
                'description' => 'Ajang silaturahmi akbar tahunan untuk seluruh alumni dari berbagai angkatan. Dimeriahkan dengan penampilan band lokal dan pameran karya.',
                'start_date' => Carbon::now()->addDays(14)->setTime(8, 0),
                'end_date' => Carbon::now()->addDays(14)->setTime(16, 0),
                'is_active' => true,
            ],
            [
                'title' => 'Webinar: Sukses Berkarir di Dunia IT (AI & Web)',
                'event_type' => 'webinar',
                'location_type' => 'online',
                'location_details' => 'Zoom Meeting (Link dibagikan via Email)',
                'description' => 'Webinar khusus alumni jurusan RPL dan TKJ tentang peluang karir di era kecerdasan buatan. Pemateri: Praktisi Industri IT.',
                'start_date' => Carbon::now()->addDays(7)->setTime(19, 0),
                'end_date' => Carbon::now()->addDays(7)->setTime(21, 0),
                'is_active' => true,
            ],
            [
                'title' => 'Job Fair & Campus Expo 2026',
                'event_type' => 'job_fair',
                'location_type' => 'offline',
                'location_details' => 'Aula Utama SMK',
                'description' => 'Bursa kerja yang diikuti oleh puluhan perusahaan rekanan sekolah. Jangan lupa bawa CV cetak dalam jumlah banyak!',
                'start_date' => Carbon::now()->addDays(20)->setTime(9, 0),
                'end_date' => Carbon::now()->addDays(22)->setTime(15, 0),
                'is_active' => true,
            ],
            [
                'title' => 'Workshop Technopreneurship',
                'event_type' => 'training',
                'location_type' => 'offline',
                'location_details' => 'Laboratorium Komputer',
                'description' => 'Workshop intensif untuk membangun bisnis startup. Kapasitas terbatas hanya untuk 50 peserta alumni pertama yang mendaftar.',
                'start_date' => Carbon::now()->subDays(5)->setTime(10, 0),
                'end_date' => Carbon::now()->subDays(5)->setTime(15, 0),
                'is_active' => false,
            ]
        ];

        foreach ($events as $event) {
            $event['created_by'] = $adminId;
            $event['slug'] = Str::slug($event['title']);
            Event::create($event);
        }
    }
}
