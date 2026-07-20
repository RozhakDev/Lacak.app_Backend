<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\JobVacancy;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\Models\JobVacancy;
use App\MoonShine\Resources\JobVacancy\Pages\JobVacancyIndexPage;
use App\MoonShine\Resources\JobVacancy\Pages\JobVacancyFormPage;
use App\MoonShine\Resources\JobVacancy\Pages\JobVacancyDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Date;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;

class JobVacancyResource extends ModelResource
{
    protected string $model = JobVacancy::class;
    protected string $title = 'Lowongan Kerja';

    protected function search(): array
    {
        return [
            'id',
            'title',
            'company_name',
        ];
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Dibuat Oleh', 'creator', 'name', \App\MoonShine\Resources\User\UserResource::class),
            Image::make('Poster/Gambar', 'images')->multiple()->disk('public')->dir('jobs'),
            Text::make('Judul Lowongan', 'title')->required(),
            Text::make('Nama Perusahaan', 'company_name')->required(),
            Date::make('Batas Waktu', 'expires_at')->nullable(),
            Switcher::make('Status Aktif', 'is_active')->default(true),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Image::make('Poster/Gambar', 'images')->multiple()->disk('public')->dir('jobs'),
            Text::make('Judul Lowongan', 'title')->required(),
            Text::make('Nama Perusahaan', 'company_name')->required(),
            Textarea::make('Deskripsi', 'description')->required(),
            Textarea::make('Persyaratan', 'requirements')->required(),
            Date::make('Batas Waktu', 'expires_at')->nullable(),
            Switcher::make('Status Aktif', 'is_active')->default(true),
        ];
    }

    protected function detailFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Dibuat Oleh', 'creator', 'name', \App\MoonShine\Resources\User\UserResource::class),
            Image::make('Poster/Gambar', 'images')->multiple()->disk('public')->dir('jobs'),
            Text::make('Judul Lowongan', 'title')->required(),
            Text::make('Nama Perusahaan', 'company_name')->required(),
            Textarea::make('Deskripsi', 'description')->required(),
            Textarea::make('Persyaratan', 'requirements')->required(),
            Date::make('Batas Waktu', 'expires_at')->nullable(),
            Switcher::make('Status Aktif', 'is_active')->default(true),
        ];
    }

    protected function beforeCreating(DataWrapperContract $item): DataWrapperContract
    {
        $model = $item->getOriginal();
        $model->created_by = auth()->id();
        return $item;
    }

    public function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'images.*' => ['nullable', 'image', 'max:2048'],
            'description' => ['required', 'string'],
            'requirements' => ['required', 'string'],
        ];
    }
}
