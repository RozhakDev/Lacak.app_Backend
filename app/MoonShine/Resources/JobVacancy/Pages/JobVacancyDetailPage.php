<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\JobVacancy\Pages;

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\JobVacancy\JobVacancyResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use Throwable;

class JobVacancyDetailPage extends DetailPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function modifyDetailComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
