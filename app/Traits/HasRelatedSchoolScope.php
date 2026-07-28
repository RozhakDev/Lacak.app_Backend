<?php

namespace App\Traits;

use App\Models\Scopes\RelatedSchoolScope;

trait HasRelatedSchoolScope
{
    protected static function bootHasRelatedSchoolScope()
    {
        static::addGlobalScope(new RelatedSchoolScope);
    }
}
