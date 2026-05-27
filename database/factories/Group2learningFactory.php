<?php

namespace Database\Factories;

use App\Models\Group2learning;
use App\Models\Group;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class Group2learningFactory extends Factory
{
    protected $model = Group2learning::class;

    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'category_id' => Category::factory(),
            'course_id' => Course::factory(),
            'parent_id' => null,
            'teacher' => fake()->name(),
            'typeOfLesson' => fake()->randomElement(['lecture', 'practice', 'exam']),
            'study_from' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'study_to' => fake()->dateTimeBetween('+2 months', '+6 months'),
        ];
    }
}
