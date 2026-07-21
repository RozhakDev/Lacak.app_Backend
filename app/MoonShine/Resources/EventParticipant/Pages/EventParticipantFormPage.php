<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\EventParticipant\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Select;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\EventParticipant\EventParticipantResource;

/**
 * @extends FormPage<EventParticipantResource>
 */
class EventParticipantFormPage extends FormPage
{
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Event', 'event', 'title', \App\MoonShine\Resources\Event\EventResource::class)->readonly(),
            BelongsTo::make('Peserta', 'user', 'name', \App\MoonShine\Resources\User\UserResource::class)->readonly(),
            Select::make('Status', 'status')->options([
                'registered' => 'Terdaftar',
                'attended' => 'Hadir',
                'cancelled' => 'Dibatalkan'
            ])->required(),
        ];
    }
}
