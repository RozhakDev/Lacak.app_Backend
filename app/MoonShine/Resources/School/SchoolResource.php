<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\School;

use Illuminate\Database\Eloquent\Model;
use App\Models\School;
use Illuminate\Contracts\Database\Eloquent\Builder;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Switcher;

class SchoolResource extends ModelResource
{
    protected string $model = School::class;
    protected string $title = 'Profil Sekolah';
    public string $column = 'name';

    protected function search(): array
    {
        return [
            'id',
            'code',
            'name',
            'email',
        ];
    }

    protected function activeActions(): \MoonShine\Support\ListOf
    {
        if (auth()->user()->hasRole('Admin BKK')) {
            return new \MoonShine\Support\ListOf(\MoonShine\Support\Enums\Action::class, [
                \MoonShine\Support\Enums\Action::VIEW,
                \MoonShine\Support\Enums\Action::UPDATE,
            ]);
        }
        return new \MoonShine\Support\ListOf(\MoonShine\Support\Enums\Action::class, [
            \MoonShine\Support\Enums\Action::CREATE,
            \MoonShine\Support\Enums\Action::VIEW,
            \MoonShine\Support\Enums\Action::UPDATE,
            \MoonShine\Support\Enums\Action::DELETE,
        ]);
    }

    public function modifyItemQueryBuilder(Builder $builder): Builder
    {
        if (auth()->user()->hasRole('Admin BKK')) {
            return $builder->where('id', auth()->user()->school_id);
        }
        return $builder;
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        if (auth()->user()->hasRole('Admin BKK')) {
            return $builder->where('id', auth()->user()->school_id);
        }
        return $builder;
    }

    protected function indexFields(): iterable
    {
        return $this->myFields();
    }

    protected function formFields(): iterable
    {
        return $this->myFields();
    }

    protected function detailFields(): iterable
    {
        return $this->myFields();
    }

    private function myFields(): array
    {
        $isAdminBkk = auth()->user()->hasRole('Admin BKK');

        return [
            ID::make()->sortable(),
            
            Text::make('Kode Sekolah', 'code')
                ->required()
                ->readonly($isAdminBkk),
                
            Text::make('Nama Sekolah', 'name')
                ->required(),
                
            Text::make('Email', 'email')
                ->required(),
                
            Text::make('Nomor Telepon', 'phone'),
            
            Text::make('Alamat', 'address'),
            
            Image::make('Logo', 'logo')
                ->disk('public')
                ->dir('schools'),
                
            Switcher::make('Status Aktif', 'is_active')
                ->readonly($isAdminBkk),
        ];
    }

    public function rules(Model $item): array
    {
        return [
            'code' => ['required', 'string', 'unique:schools,code,' . $item->getKey()],
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:schools,email,' . $item->getKey()],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'logo' => ['nullable', 'image'],
            'is_active' => ['boolean'],
        ];
    }
}
