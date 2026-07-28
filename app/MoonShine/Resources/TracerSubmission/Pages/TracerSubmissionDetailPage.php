<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\TracerSubmission\Pages;

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\TracerSubmission\TracerSubmissionResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use Throwable;

class TracerSubmissionDetailPage extends DetailPage
{


    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function modifyEditButton(\MoonShine\Contracts\UI\ActionButtonContract $button): \MoonShine\Contracts\UI\ActionButtonContract
    {
        return \MoonShine\UI\Components\ActionButton::emptyHidden();
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
