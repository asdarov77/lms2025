<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_requires_query()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/search?type=questions');

        $response->assertStatus(422);
    }
}
