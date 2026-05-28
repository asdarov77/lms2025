<?php

namespace Tests\Feature\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_categories_list()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_category()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'title' => 'Test Category',
        ];

        $response = $this->postJson('/api/categories', $data);

        $response->assertStatus(201);
    }

    public function test_cannot_create_category_without_title()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/categories', []);

        $response->assertStatus(422);
    }
}
