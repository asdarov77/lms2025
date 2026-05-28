<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GiftControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_cannot_import_gift()
    {
        $response = $this->postJson('/api/gift/import', ['content' => 'test']);

        $response->assertStatus(401);
    }
}
