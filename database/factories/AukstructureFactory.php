<?php

namespace Database\Factories;

use App\Models\Aukstructure;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class AukstructureFactory extends Factory
{
    protected $model = Aukstructure::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'parent_id' => null,
            'title' => fake()->unique()->words(3, true),
            'type' => fake()->numberBetween(1, 3),
            'description' => fake()->sentence(),
            'identifier' => fake()->unique()->uuid(),
        ];
    }

    public function withParent($parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }
}
