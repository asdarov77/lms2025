<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'aircraft_id',
        'title',
        'path',
        'short_description',
        'long_description',
        'visible',
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_course');
    }

    public function aukstructures(): HasMany
    {
        return $this->hasMany(Aukstructure::class);
    }

    public function group2learnings(): HasMany
    {
        return $this->hasMany(Group2learning::class);
    }
}
