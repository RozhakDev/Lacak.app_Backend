<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Role;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use App\MoonShine\Resources\Role\Pages\RoleIndexPage;
use App\MoonShine\Resources\Role\Pages\RoleFormPage;
use App\MoonShine\Resources\Role\Pages\RoleDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

class RoleResource extends ModelResource
{
    protected string $model = Role::class;
    protected string $title = 'Roles';

    protected function pages(): array
    {
        return [
            RoleIndexPage::class,
            RoleFormPage::class,
            RoleDetailPage::class,
        ];
    }
}
