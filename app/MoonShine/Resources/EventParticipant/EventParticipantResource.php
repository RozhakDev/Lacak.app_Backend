<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\EventParticipant;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventParticipant;
use App\MoonShine\Resources\EventParticipant\Pages\EventParticipantIndexPage;
use App\MoonShine\Resources\EventParticipant\Pages\EventParticipantFormPage;
use App\MoonShine\Resources\EventParticipant\Pages\EventParticipantDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

class EventParticipantResource extends ModelResource
{
    protected string $model = EventParticipant::class;
    protected string $title = 'Peserta Event';
    protected bool $withPolicy = false;

    protected function activeActions(): ListOf
    {
        return new ListOf(Action::class, [
            Action::VIEW,
            Action::UPDATE,
            Action::DELETE,
            Action::MASS_DELETE,
        ]);
    }

    protected function pages(): array
    {
        return [
            EventParticipantIndexPage::class,
            EventParticipantFormPage::class,
            EventParticipantDetailPage::class,
        ];
    }
}
