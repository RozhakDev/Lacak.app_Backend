<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\JobApplication;

use Illuminate\Database\Eloquent\Model;
use App\Models\JobApplication;
use App\MoonShine\Resources\JobApplication\Pages\JobApplicationIndexPage;
use App\MoonShine\Resources\JobApplication\Pages\JobApplicationFormPage;
use App\MoonShine\Resources\JobApplication\Pages\JobApplicationDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Select;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

class JobApplicationResource extends ModelResource
{
    protected string $model = JobApplication::class;
    protected string $title = 'Lamaran Kerja';

    protected function activeActions(): ListOf
    {
        return new ListOf(Action::class, [
            Action::VIEW,
            Action::UPDATE,
            Action::DELETE,
            Action::MASS_DELETE,
        ]);
    }

    protected function search(): array
    {
        return [
            'id',
            'status',
        ];
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Lowongan', 'jobVacancy', 'title', \App\MoonShine\Resources\JobVacancy\JobVacancyResource::class)->sortable(),
            BelongsTo::make('Pelamar', 'user', 'name', \App\MoonShine\Resources\User\UserResource::class)->sortable(),
            File::make('CV', 'cv_url')->disk('public')->dir('job_applications/cv'),
            Select::make('Status', 'status')->options([
                'pending' => 'Menunggu Review',
                'reviewed' => 'Sedang Direview',
                'accepted' => 'Diterima',
                'rejected' => 'Ditolak'
            ]),
        ];
    }

    protected function formFields(): iterable
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

    protected function detailFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Lowongan', 'jobVacancy', 'title', \App\MoonShine\Resources\JobVacancy\JobVacancyResource::class),
            BelongsTo::make('Pelamar', 'user', 'name', \App\MoonShine\Resources\User\UserResource::class),
            File::make('CV', 'cv_url')->disk('public')->dir('job_applications/cv'),
            Textarea::make('Cover Letter', 'cover_letter')->nullable(),
            Select::make('Status', 'status')->options([
                'pending' => 'Menunggu Review',
                'reviewed' => 'Sedang Direview',
                'accepted' => 'Diterima',
                'rejected' => 'Ditolak'
            ]),
        ];
    }

    protected function pages(): array
    {
        return [
            JobApplicationIndexPage::class,
            JobApplicationFormPage::class,
            JobApplicationDetailPage::class,
        ];
    }
}
