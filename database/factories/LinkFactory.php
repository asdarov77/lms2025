<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\Aukstructure;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkFactory extends Factory
{
    protected $model = Link::class;

    public function definition(): array
    {
        return [
            'aukstructure_id' => Aukstructure::factory(),
            'link' => fake()->unique()->word() . '.html',
        ];
    }
}
