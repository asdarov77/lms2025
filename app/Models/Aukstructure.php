<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Aukstructure extends Model
{
    protected $fillable = [
        'course_id',
        'parent_id',
        'title',
        'type',
        'description',
        'identifier',
        'categories',
    ];

    protected $casts = [
        'type' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Aukstructure::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Aukstructure::class, 'parent_id');
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'aukstructure_category');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
