<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\LoginPage as BaseLoginPage;
use MoonShine\Core\Attributes\Layout;
use App\MoonShine\Layouts\MoonShineLoginLayout;
use MoonShine\MenuManager\Attributes\SkipMenu;

#[SkipMenu]
#[Layout(MoonShineLoginLayout::class)]
class LoginPage extends BaseLoginPage
{
}
