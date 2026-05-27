<?php

namespace Tests\Unit\Models;

use App\Models\Aircraft;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AircraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_aircraft_has_fillable_attributes(): void
    {
        $aircraft = new Aircraft();
        $fillable = $aircraft->getFillable();

        $this->assertContains('title', $fillable);
        $this->assertContains('path', $fillable);
    }

    public function test_aircraft_can_have_courses(): void
    {
        $aircraft = Aircraft::factory()->create();
        $courses = Course::factory()->count(3)->create(['aircraft_id' => $aircraft->id]);

        $this->assertEquals(3, $aircraft->courses->count());
    }

    public function test_aircraft_can_have_categories(): void
    {
        $aircraft = Aircraft::factory()->create();
        $categories = Category::factory()->count(2)->create(['aircraft_id' => $aircraft->id]);

        $this->assertEquals(2, $aircraft->categories->count());
    }

    public function test_aircraft_path_is_unique(): void
    {
        $aircraft1 = Aircraft::factory()->create();
        
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        Aircraft::factory()->create(['path' => $aircraft1->path]);
    }
}
