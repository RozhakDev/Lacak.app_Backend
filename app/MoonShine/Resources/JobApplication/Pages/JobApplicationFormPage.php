<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\JobApplication\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\JobApplication\JobApplicationResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use Throwable;


/**
 * @extends FormPage<JobApplicationResource>
 */
class JobApplicationFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            BelongsTo::make('Lowongan', 'jobVacancy', 'title', \App\MoonShine\Resources\JobVacancy\JobVacancyResource::class)->required(),
            BelongsTo::make('Pelamar', 'user', 'name', \App\MoonShine\Resources\User\UserResource::class)->required(),
            File::make('CV', 'cv_url')->disk('public')->dir('job_applications/cv'),
            Textarea::make('Cover Letter', 'cover_letter')->nullable(),
            Select::make('Status', 'status')->options([
                'pending' => 'Menunggu Review',
                'reviewed' => 'Sedang Direview',
                'accepted' => 'Diterima',
                'rejected' => 'Ditolak'
            ])->required(),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function formButtons(): ListOf
    {
        return parent::formButtons();
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [];
    }

    /**
     * @param  FormBuilder  $component
     *
     * @return FormBuilder
     */
    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
    {
        return $component;
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
