<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Aircraft;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'aircraft_id' => Aircraft::factory(),
            'title' => fake()->unique()->words(3, true),
            'code' => fake()->unique()->bothify('???-###'),
            'description' => fake()->sentence(),
        ];
    }
}
