<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    protected $fillable = [
        'groupname',
        'groupdescription',
    ];

    public function group2learnings(): HasMany
    {
        return $this->hasMany(Group2learning::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
