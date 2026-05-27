<?php

namespace Database\Factories;

use App\Models\GradeBoundary;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeBoundaryFactory extends Factory
{
    protected $model = GradeBoundary::class;

    public function definition(): array
    {
        return [
            'boundary' => fake()->numberBetween(0, 100),
            'grade' => fake()->numberBetween(2, 5),
        ];
    }
}
