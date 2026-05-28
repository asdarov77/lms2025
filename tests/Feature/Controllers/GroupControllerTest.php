<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_cannot_access_groups()
    {
        $response = $this->getJson('/api/groups');

        $response->assertStatus(401);
    }
}
