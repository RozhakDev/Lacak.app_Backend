<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Event\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Date;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\Event\EventResource;

/**
 * @extends FormPage<EventResource>
 */
class EventFormPage extends FormPage
{
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Judul Kegiatan', 'title')->required(),
            Image::make('Banner', 'banner_url')
                ->disk('public')
                ->dir('events')
                ->removable()
                ->nullable(),
            Textarea::make('Deskripsi', 'description')->required(),
            Select::make('Tipe Event', 'event_type')->options([
                'webinar' => 'Webinar',
                'bootcamp' => 'Bootcamp',
                'training' => 'Pelatihan',
                'job_fair' => 'Job Fair',
                'other' => 'Lainnya'
            ])->required(),
            Select::make('Lokasi', 'location_type')->options([
                'online' => 'Online (Zoom/Meet)',
                'offline' => 'Offline (Tatap Muka)',
                'hybrid' => 'Hybrid',
            ])->required(),
            Text::make('Detail Lokasi / Link', 'location_details')->nullable(),
            Date::make('Tanggal Mulai', 'start_date')->withTime()->required(),
            Date::make('Tanggal Selesai', 'end_date')->withTime()->nullable(),
            Switcher::make('Aktif', 'is_active')->default(true),
        ];
    }
}
