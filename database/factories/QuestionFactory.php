<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Category;
use App\Models\Aukstructure;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'aukstructure_id' => Aukstructure::factory(),
            'question_text' => fake()->sentence(),
        ];
    }
}
