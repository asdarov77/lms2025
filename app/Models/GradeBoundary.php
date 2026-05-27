<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeBoundary extends Model
{
    protected $fillable = [
        'boundary',
        'grade',
    ];

    public static function getGrade(int $percentage): int
    {
        $boundaries = self::orderBy('boundary', 'desc')->get();
        
        foreach ($boundaries as $boundary) {
            if ($percentage >= $boundary->boundary) {
                return $boundary->grade;
            }
        }
        
        return 2; // Default minimum grade
    }
}
