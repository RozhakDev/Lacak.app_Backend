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
    
    protected function beforeSaving(DataWrapperContract $item): DataWrapperContract
    {
        $model = $item->getOriginal();
        
        if (request()->filled('password')) {
            $model->password = Hash::make(request()->input('password'));
        } else {
            request()->request->remove('password');
            unset($model->password);
        }

        return $item;
    }
}
