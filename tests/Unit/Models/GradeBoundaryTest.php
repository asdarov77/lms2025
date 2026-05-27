<?php

namespace Tests\Unit\Models;

use App\Models\GradeBoundary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeBoundaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_grade_boundary_has_fillable_attributes(): void
    {
        $boundary = new GradeBoundary();
        $fillable = $boundary->getFillable();

        $this->assertContains('boundary', $fillable);
        $this->assertContains('grade', $fillable);
    }

    public function test_get_grade_returns_correct_grade(): void
    {
        GradeBoundary::create(['boundary' => 90, 'grade' => 5]);
        GradeBoundary::create(['boundary' => 75, 'grade' => 4]);
        GradeBoundary::create(['boundary' => 50, 'grade' => 3]);

        $this->assertEquals(5, GradeBoundary::getGrade(95));
        $this->assertEquals(5, GradeBoundary::getGrade(90));
        $this->assertEquals(4, GradeBoundary::getGrade(80));
        $this->assertEquals(3, GradeBoundary::getGrade(60));
        $this->assertEquals(2, GradeBoundary::getGrade(40));
    }

    public function test_get_grade_returns_default_grade_when_no_boundaries(): void
    {
        $this->assertEquals(2, GradeBoundary::getGrade(100));
    }

    public function test_get_grade_with_exact_boundary(): void
    {
        GradeBoundary::create(['boundary' => 80, 'grade' => 5]);
        
        $this->assertEquals(5, GradeBoundary::getGrade(80));
        $this->assertEquals(2, GradeBoundary::getGrade(79));
    }
}
