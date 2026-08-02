<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $eventsData = [
            'SMKN1' => [
                [
                    'title' => 'Webinar: Sukses Berkarir di Dunia IT (AI & Web)',
                    'event_type' => 'webinar',
                    'location_type' => 'online',
                    'location_details' => 'Zoom Meeting',
                    'description' => 'Webinar khusus alumni jurusan RPL dan TKJ tentang peluang karir di era kecerdasan buatan.',
                    'start_date' => Carbon::now()->addDays(7)->setTime(19, 0),
                    'end_date' => Carbon::now()->addDays(7)->setTime(21, 0),
                    'is_active' => true,
                ],
                [
                    'title' => 'Workshop Technopreneurship',
                    'event_type' => 'training',
                    'location_type' => 'offline',
                    'location_details' => 'Laboratorium Komputer SMKN 1',
                    'description' => 'Workshop intensif untuk membangun bisnis startup teknologi.',
                    'start_date' => Carbon::now()->addDays(14)->setTime(10, 0),
                    'end_date' => Carbon::now()->addDays(14)->setTime(15, 0),
                    'is_active' => true,
                ],
            ],
            'SMKN2' => [
                [
                    'title' => 'Seminar Kewirausahaan & Strategi Pemasaran Digital',
                    'event_type' => 'training',
                    'location_type' => 'offline',
                    'location_details' => 'Aula SMKN 2',
                    'description' => 'Strategi berjualan di platform e-commerce dan social media marketing.',
                    'start_date' => Carbon::now()->addDays(10)->setTime(9, 0),
                    'end_date' => Carbon::now()->addDays(10)->setTime(12, 0),
                    'is_active' => true,
                ],
            ],
            'SMKN3' => [
                [
                    'title' => 'Festival Kuliner Nusantara',
                    'event_type' => 'other',
                    'location_type' => 'offline',
                    'location_details' => 'Lobby Utama SMKN 3',
                    'description' => 'Pameran dan kompetisi memasak alumni jurusan Tata Boga.',
                    'start_date' => Carbon::now()->addDays(20)->setTime(8, 0),
                    'end_date' => Carbon::now()->addDays(20)->setTime(15, 0),
                    'is_active' => true,
                ],
            ]
        ];

        $schools = School::all();
        if ($schools->isEmpty()) return;

        foreach ($schools as $school) {
            $bkk = User::where('school_id', $school->id)->role('Admin BKK')->first();
            $creatorId = $bkk ? $bkk->id : $adminId;

            if (isset($eventsData[$school->code])) {
                foreach ($eventsData[$school->code] as $event) {
                    $event['created_by'] = $creatorId;
                    $event['school_id'] = $school->id;
                    $event['slug'] = Str::slug($event['title'] . '-' . $school->code);
                    Event::create($event);
                }
            }
        }
    }
}
