<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GradeBoundaryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_cannot_access_grade_boundaries()
    {
        $response = $this->getJson('/api/grade-boundaries');

        $response->assertStatus(401);
    }
}
