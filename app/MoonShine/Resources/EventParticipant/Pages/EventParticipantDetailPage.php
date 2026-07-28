<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\EventParticipant\Pages;

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Select;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\EventParticipant\EventParticipantResource;
use App\MoonShine\Resources\Event\EventResource;
use App\MoonShine\Resources\User\UserResource;

class EventParticipantDetailPage extends DetailPage
{
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Event', 'event', 'title', EventResource::class),
            BelongsTo::make('Peserta', 'user', 'name', UserResource::class),
            Text::make('Status', 'status'),
        ];
    }
}
