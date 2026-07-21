<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Event\Pages;

use MoonShine\Laravel\Pages\Crud\DetailPage;
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
 * @extends DetailPage<EventResource>
 */
class EventDetailPage extends DetailPage
{
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Judul Kegiatan', 'title'),
            Image::make('Banner', 'banner_url')
                ->disk('public')
                ->dir('events'),
            Textarea::make('Deskripsi', 'description'),
            Text::make('Tipe Event', 'event_type'),
            Text::make('Lokasi', 'location_type'),
            Text::make('Detail Lokasi / Link', 'location_details'),
            Date::make('Tanggal Mulai', 'start_date')->withTime(),
            Date::make('Tanggal Selesai', 'end_date')->withTime(),
            Switcher::make('Aktif', 'is_active'),
        ];
    }
}
