<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\User;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\MoonShine\Resources\User\Pages\UserIndexPage;
use App\MoonShine\Resources\User\Pages\UserFormPage;
use App\MoonShine\Resources\User\Pages\UserDetailPage;
use Illuminate\Support\Facades\Hash;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

class UserResource extends ModelResource
{
    protected string $model = User::class;
    protected string $title = 'Pengguna (Users)';
    protected string $column = 'name';

    protected function pages(): array
    {
        return [
            UserIndexPage::class,
            UserFormPage::class,
            UserDetailPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
            'email',
            'nisn',
        ];
    }
    
    protected function beforeCreating(DataWrapperContract $item): DataWrapperContract
    {
        $model = $item->getOriginal();
        $user = auth()->user();

        if (!$user->hasRole('Super Admin')) {
            $model->school_id = $user->school_id;
        }

        if (request()->filled('password')) {
            $model->password = Hash::make(request()->input('password'));
        }

        if (empty($model->email_verified_at)) {
            $model->email_verified_at = now();
        }

        return $item;
    }

    protected function beforeUpdating(DataWrapperContract $item): DataWrapperContract
    {
        $model = $item->getOriginal();
        $user = auth()->user();

        if (!$user->hasRole('Super Admin')) {
            $model->school_id = $user->school_id;
        }

        if (request()->filled('password')) {
            $model->password = Hash::make(request()->input('password'));
        } else {
            request()->request->remove('password');
            unset($model->password);
        }

        return $item;
    }
}
