<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Aircraft;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'aircraft_id' => Aircraft::factory(),
            'title' => fake()->unique()->words(3, true),
            'path' => fake()->unique()->slug(),
            'short_description' => fake()->sentence(),
            'long_description' => fake()->paragraph(),
            'visible' => fake()->boolean(),
        ];
    }

    public function visible(): static
    {
        return $this->state(fn (array $attributes) => [
            'visible' => true,
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'visible' => false,
        ]);
    }
}
