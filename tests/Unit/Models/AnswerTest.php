<?php

namespace Tests\Unit\Models;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnswerTest extends TestCase
{
    use RefreshDatabase;

    public function test_answer_has_fillable_attributes(): void
    {
        $answer = new Answer();
        $fillable = $answer->getFillable();

        $this->assertContains('question_id', $fillable);
        $this->assertContains('answer', $fillable);
        $this->assertContains('is_correct', $fillable);
    }

    public function test_is_correct_is_cast_to_boolean(): void
    {
        $answer = Answer::factory()->create(['is_correct' => true]);
        
        $this->assertTrue($answer->is_correct);
        $this->assertIsBool($answer->is_correct);
    }

    public function test_answer_belongs_to_question(): void
    {
        $question = Question::factory()->create();
        $answer = Answer::factory()->create(['question_id' => $question->id]);

        $this->assertEquals($question->id, $answer->question->id);
    }

    public function test_correct_answer_state(): void
    {
        $answer = Answer::factory()->correct()->create();
        
        $this->assertTrue($answer->is_correct);
    }

    public function test_incorrect_answer_state(): void
    {
        $answer = Answer::factory()->incorrect()->create();
        
        $this->assertFalse($answer->is_correct);
    }
}
