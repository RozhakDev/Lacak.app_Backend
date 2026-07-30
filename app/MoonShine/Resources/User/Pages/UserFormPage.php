<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\User\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\User\UserResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Password;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Select;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use App\MoonShine\Resources\School\SchoolResource;
use MoonShine\UI\Components\Layout\Box;
use Throwable;

class UserFormPage extends FormPage
{
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make()->sortable(),
                Text::make('Nama Lengkap', 'name')->required(),
                Email::make('Email', 'email')->required(),
                Text::make('NISN', 'nisn')->nullable(),
                Password::make('Password', 'password')
                    ->hint('Kosongkan jika tidak ingin mengubah password.')
                    ->canSee(fn(Password $field) => request()->route('resourceItem')),
                Password::make('Password', 'password')
                    ->required()
                    ->canSee(fn(Password $field) => !request()->route('resourceItem')),
                Select::make('Hak Akses', 'primary_role')
                    ->options(function () {
                        $query = Role::query();
                        if (!auth()->user()->hasRole('Super Admin')) {
                            $query->where('name', '!=', 'Super Admin');
                        }
                        return $query->pluck('name', 'name')->toArray();
                    })
                    ->fill(fn($model) => $model?->roles?->first()?->name)
                    ->onApply(fn(Model $item, $value) => $item)
                    ->onAfterApply(function (Model $item, $value) {
                        $item->syncRoles([$value]);
                        return $item;
                    })
                    ->required(),
                BelongsTo::make('Sekolah', 'school', 'name', SchoolResource::class)
                    ->nullable(fn() => auth()->user()->hasRole('Super Admin'))
                    ->hint(auth()->user()->hasRole('Super Admin') ? 'Kosongkan untuk Super Admin (Bisa akses semua sekolah)' : 'Otomatis terikat dengan instansi Anda.')
                    ->default(auth()->user()->school_id)
                    ->valuesQuery(function (Builder $query) {
                        if (!auth()->user()->hasRole('Super Admin')) {
                            return $query->where('id', auth()->user()->school_id);
                        }
                        return $query;
                    })
                    ->onApply(function (Model $item, $value) {
                        if (!auth()->user()->hasRole('Super Admin')) {
                            $item->school_id = auth()->user()->school_id;
                        } else {
                            $item->school_id = empty($value) ? null : $value;
                        }
                        return $item;
                    }),
            ]),
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . ($item->getOriginal()->id ?? '')],
            'nisn' => ['nullable', 'string', 'max:255', 'unique:users,nisn,' . ($item->getOriginal()->id ?? '')],
        ];
    }

    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
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
