<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group2learning extends Model
{
    protected $fillable = [
        'group_id',
        'category_id',
        'course_id',
        'parent_id',
        'teacher',
        'typeOfLesson',
        'study_from',
        'study_to',
    ];

    protected $casts = [
        'study_from' => 'date',
        'study_to' => 'date',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Group2learning::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Group2learning::class, 'parent_id');
    }
}
