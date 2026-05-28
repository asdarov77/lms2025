<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_favorites()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/favorites');

        $response->assertStatus(200);
    }

    public function test_unauthorized_cannot_get_favorites()
    {
        $response = $this->getJson('/api/favorites');

        $response->assertStatus(401);
    }
}
