<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScoreCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_calculates_score_for_all_correct_answers()
    {
        // Create quiz with 3 questions worth 10 points each
        $quiz = Quiz::create([
            'title' => 'Test Quiz',
            'slug' => 'test-quiz',
            'time_limit' => 30,
            'is_active' => true,
        ]);

        $questions = [];
        for ($i = 1; $i <= 3; $i++) {
            $q = Question::create([
                'quiz_id' => $quiz->id,
                'content' => "Question $i",
                'type' => 'multiple_choice',
                'points' => 10,
            ]);
            
            // Create correct option
            Option::create([
                'question_id' => $q->id,
                'option_text' => 'Correct',
                'is_correct' => true,
            ]);
            
            $questions[] = $q;
        }

        // Create attempt
        $attempt = QuizAttempt::create([
            'user_id' => $this->user->id,
            'quiz_id' => $quiz->id,
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        // Create all correct answers
        foreach ($questions as $q) {
            QuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $q->id,
                'user_answer' => $q->options->first()->id,
                'is_correct' => true,
            ]);
        }

        // Calculate score
        $score = $attempt->calculateScore();

        // Assert score is 30 (3 questions * 10 points)
        $this->assertEquals(30, $score);
        $this->assertEquals(30, $attempt->total_score);
    }

    /** @test */
    public function it_calculates_score_for_partial_correct_answers()
    {
        $quiz = Quiz::create([
            'title' => 'Test Quiz',
            'slug' => 'test-quiz-partial',
            'time_limit' => 30,
            'is_active' => true,
        ]);

        // Create 5 questions worth 10 points each
        $questions = [];
        for ($i = 1; $i <= 5; $i++) {
            $q = Question::create([
                'quiz_id' => $quiz->id,
                'content' => "Question $i",
                'type' => 'multiple_choice',
                'points' => 10,
            ]);
            $questions[] = $q;
        }

        $attempt = QuizAttempt::create([
            'user_id' => $this->user->id,
            'quiz_id' => $quiz->id,
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        // Answer 3 out of 5 correctly
        foreach ($questions as $idx => $q) {
            QuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $q->id,
                'user_answer' => 'some_answer',
                'is_correct' => $idx < 3, // First 3 correct
            ]);
        }

        $score = $attempt->calculateScore();

        // Assert score is 30 (3 correct * 10 points)
        $this->assertEquals(30, $score);
    }

    /** @test */
    public function it_calculates_score_with_different_point_values()
    {
        $quiz = Quiz::create([
            'title' => 'Mixed Points Quiz',
            'slug' => 'mixed-points',
            'time_limit' => 30,
            'is_active' => true,
        ]);

        // Easy question: 5 points
        $q1 = Question::create([
            'quiz_id' => $quiz->id,
            'content' => 'Easy',
            'type' => 'multiple_choice',
            'points' => 5,
        ]);

        // Medium question: 10 points
        $q2 = Question::create([
            'quiz_id' => $quiz->id,
            'content' => 'Medium',
            'type' => 'multiple_choice',
            'points' => 10,
        ]);

        // Hard question: 20 points
        $q3 = Question::create([
            'quiz_id' => $quiz->id,
            'content' => 'Hard',
            'type' => 'multiple_choice',
            'points' => 20,
        ]);

        $attempt = QuizAttempt::create([
            'user_id' => $this->user->id,
            'quiz_id' => $quiz->id,
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        // Answer easy and hard correctly
        QuizAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $q1->id,
            'user_answer' => 'a',
            'is_correct' => true,
        ]);
        QuizAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $q2->id,
            'user_answer' => 'b',
            'is_correct' => false,
        ]);
        QuizAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $q3->id,
            'user_answer' => 'c',
            'is_correct' => true,
        ]);

        $score = $attempt->calculateScore();

        // Assert score is 25 (5 + 20)
        $this->assertEquals(25, $score);
    }

    /** @test */
    public function it_returns_zero_for_no_correct_answers()
    {
        $quiz = Quiz::create([
            'title' => 'Zero Score Quiz',
            'slug' => 'zero-score',
            'time_limit' => 30,
            'is_active' => true,
        ]);

        $q = Question::create([
            'quiz_id' => $quiz->id,
            'content' => 'Q1',
            'type' => 'multiple_choice',
            'points' => 10,
        ]);

        $attempt = QuizAttempt::create([
            'user_id' => $this->user->id,
            'quiz_id' => $quiz->id,
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        QuizAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $q->id,
            'user_answer' => 'wrong',
            'is_correct' => false,
        ]);

        $score = $attempt->calculateScore();

        $this->assertEquals(0, $score);
    }

    /** @test */
    public function time_expired_check_works_correctly()
    {
        $quiz = Quiz::create([
            'title' => 'Timed Quiz',
            'slug' => 'timed',
            'time_limit' => 30, // 30 minutes
            'is_active' => true,
        ]);

        // Attempt started 31 minutes ago
        $attempt = QuizAttempt::create([
            'user_id' => $this->user->id,
            'quiz_id' => $quiz->id,
            'start_time' => now()->subMinutes(31),
            'status' => 'in_progress',
        ]);

        $this->assertTrue($attempt->isTimeExpired());

        // Attempt started 10 minutes ago
        $attempt2 = QuizAttempt::create([
            'user_id' => $this->user->id,
            'quiz_id' => $quiz->id,
            'start_time' => now()->subMinutes(10),
            'status' => 'in_progress',
        ]);

        $this->assertFalse($attempt2->isTimeExpired());
    }
}
