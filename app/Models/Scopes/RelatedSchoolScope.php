<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class RelatedSchoolScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::hasUser()) {
            $user = Auth::user();
            if (!$user->hasRole('Super Admin')) {
                $relation = method_exists($model, 'getSchoolRelationPath') 
                    ? $model->getSchoolRelationPath() 
                    : 'user';
                
                $builder->whereHas($relation, function ($query) use ($user) {
                    $query->where('school_id', $user->school_id);
                });
            }
        }
    }
}
