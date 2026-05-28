<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_cannot_access_permissions()
    {
        $response = $this->getJson('/api/permissions');

        $response->assertStatus(401);
    }
}
