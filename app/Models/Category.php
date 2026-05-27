<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'aircraft_id',
        'title',
        'code',
        'description',
    ];

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'category_course');
    }

    public function aukstructures(): BelongsToMany
    {
        return $this->belongsToMany(Aukstructure::class, 'aukstructure_category');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function group2learnings(): HasMany
    {
        return $this->hasMany(Group2learning::class);
    }
}
