<?php

namespace App\Traits;

use App\Models\Scopes\SchoolScope;

trait HasSchoolScope
{
    protected static function bootHasSchoolScope()
    {
        static::addGlobalScope(new SchoolScope);
    }
}
