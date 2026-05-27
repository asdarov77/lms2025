<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends Model
{
    protected $fillable = [
        'aukstructure_id',
        'link',
    ];

    public function aukstructure(): BelongsTo
    {
        return $this->belongsTo(Aukstructure::class);
    }
}
