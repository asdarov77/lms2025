<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'groupname' => fake()->unique()->words(2, true),
            'groupdescription' => fake()->sentence(),
        ];
    }
}
