<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_cannot_access_courses()
    {
        $response = $this->getJson('/api/courses');

        $response->assertStatus(401);
    }
}
