<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\EventParticipant\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Select;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\EventParticipant\EventParticipantResource;

/**
 * @extends IndexPage<EventParticipantResource>
 */
class EventParticipantIndexPage extends IndexPage
{
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Event', 'event', 'title', \App\MoonShine\Resources\Event\EventResource::class)->sortable(),
            BelongsTo::make('Peserta', 'user', 'name', \App\MoonShine\Resources\User\UserResource::class)->sortable(),
            Select::make('Status', 'status')->options([
                'registered' => 'Terdaftar',
                'attended' => 'Hadir',
                'cancelled' => 'Dibatalkan'
            ])->updateOnPreview(),
        ];
    }
}
