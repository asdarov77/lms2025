<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PrivateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_cannot_access_private()
    {
        $response = $this->getJson('/api/private/test/test');

        $response->assertStatus(401);
    }
}
