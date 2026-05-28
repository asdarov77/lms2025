<?php

namespace Tests\Feature\Controllers;

use App\Models\Question;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class QuestionsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_questions_list()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Question::factory()->count(3)->create();

        $response = $this->getJson('/api/questions');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_question()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::factory()->create();
        $data = [
            'question_text' => 'Test Question',
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/questions', $data);

        $response->assertStatus(201);
    }

    public function test_cannot_create_question_without_text()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::factory()->create();
        $response = $this->postJson('/api/questions', ['category_id' => $category->id]);

        $response->assertStatus(422);
    }
}
