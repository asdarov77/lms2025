<?php

namespace Tests\Feature\Controllers;

use App\Models\Aircraft;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use Tests\TestCase;

class AircraftControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_aircraft_list(): void
    {
        Aircraft::factory()->count(3)->create();

        $response = $this->getJson('/api/aircrafts');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_get_single_aircraft(): void
    {
        $aircraft = Aircraft::factory()->create();

        $response = $this->getJson("/api/aircrafts/{$aircraft->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $aircraft->id]);
    }

    public function test_unauthorized_user_cannot_create_aircraft(): void
    {
        $response = $this->postJson('/api/aircrafts', [
            'title' => 'Test Aircraft',
            'path' => 'test-aircraft',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_aircraft(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/aircrafts', [
            'title' => 'Test Aircraft',
            'path' => 'test-aircraft',
        ]);

        $response->assertStatus(201)
            ->assertJson(['title' => 'Test Aircraft', 'path' => 'test-aircraft']);
    }

    public function test_aircraft_path_must_be_unique(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        Aircraft::factory()->create(['path' => 'existing-path']);

        $response = $this->postJson('/api/aircrafts', [
            'title' => 'Another Aircraft',
            'path' => 'existing-path',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('path');
    }
}
