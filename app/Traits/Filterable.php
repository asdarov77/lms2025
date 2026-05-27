<?php

namespace App\Traits;

use App\Http\Filters\AbstractFilter;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, AbstractFilter $filter)
    {
        return $filter->apply($query);
    }
}
