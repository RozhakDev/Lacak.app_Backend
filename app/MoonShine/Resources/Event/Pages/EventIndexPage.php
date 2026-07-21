<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Event\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Date;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\Event\EventResource;

/**
 * @extends IndexPage<EventResource>
 */
class EventIndexPage extends IndexPage
{
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Image::make('Banner', 'banner_url')->disk('public')->dir('events'),
            Text::make('Judul', 'title')->sortable(),
            Select::make('Tipe Event', 'event_type')->options([
                'webinar' => 'Webinar',
                'bootcamp' => 'Bootcamp',
                'training' => 'Pelatihan',
                'job_fair' => 'Job Fair',
                'other' => 'Lainnya'
            ])->sortable(),
            Select::make('Lokasi', 'location_type')->options([
                'online' => 'Online',
                'offline' => 'Offline',
                'hybrid' => 'Hybrid',
            ])->sortable(),
            Date::make('Mulai', 'start_date')->format('Y-m-d H:i')->sortable(),
            Switcher::make('Aktif', 'is_active')->updateOnPreview(),
        ];
    }
}
