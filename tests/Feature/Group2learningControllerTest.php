<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Group2learningControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_cannot_access_group2learning()
    {
        $response = $this->getJson('/api/group2learnings');

        $response->assertStatus(401);
    }
}
