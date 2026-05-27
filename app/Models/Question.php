<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'category_id',
        'aukstructure_id',
        'question_text',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function aukstructure(): BelongsTo
    {
        return $this->belongsTo(Aukstructure::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
