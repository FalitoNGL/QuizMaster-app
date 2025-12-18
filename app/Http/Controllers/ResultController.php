<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    /**
     * Display result after completing quiz
     */
    public function show(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($attempt->status !== 'completed') {
            return redirect()->route('exam.show', $attempt);
        }

        $attempt->load(['quiz', 'answers.question.options']);

        // Calculate statistics
        $totalQuestions = $attempt->quiz->questions()->count();
        $correctAnswers = $attempt->answers->where('is_correct', true)->count();
        $wrongAnswers = $attempt->answers->where('is_correct', false)->count();
        $unanswered = $totalQuestions - $attempt->answers->count();

        $maxScore = $attempt->quiz->total_points;
        $percentage = $maxScore > 0 ? round(($attempt->total_score / $maxScore) * 100) : 0;

        // Determine grade
        $grade = match(true) {
            $percentage >= 90 => 'A',
            $percentage >= 80 => 'B',
            $percentage >= 70 => 'C',
            $percentage >= 60 => 'D',
            default => 'E',
        };

        return view('quiz.result', [
            'attempt' => $attempt,
            'quiz' => $attempt->quiz,
            'stats' => [
                'total_questions' => $totalQuestions,
                'correct' => $correctAnswers,
                'wrong' => $wrongAnswers,
                'unanswered' => $unanswered,
                'score' => $attempt->total_score,
                'max_score' => $maxScore,
                'percentage' => $percentage,
                'grade' => $grade,
            ],
            'xp_earned' => session('xp_earned', 0),
        ]);
    }

    /**
     * Display detailed review with correct answers
     */
    public function review(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($attempt->status !== 'completed') {
            return redirect()->route('exam.show', $attempt);
        }

        $attempt->load(['quiz.questions.options', 'answers']);

        $reviewData = $attempt->quiz->questions->map(function ($question) use ($attempt) {
            $userAnswer = $attempt->answers->firstWhere('question_id', $question->id);
            
            return [
                'question' => $question,
                'user_answer' => $userAnswer?->user_answer,
                'is_correct' => $userAnswer?->is_correct ?? false,
                'correct_answer' => $this->getCorrectAnswer($question),
            ];
        });

        return view('quiz.review', [
            'attempt' => $attempt,
            'quiz' => $attempt->quiz,
            'reviewData' => $reviewData,
        ]);
    }

    /**
     * Get correct answer for display
     */
    private function getCorrectAnswer($question)
    {
        switch ($question->type) {
            case 'single_choice':
            case 'multiple_choice':
                return $question->correctOptions->pluck('option_text');
            case 'ordering':
                return $question->options->sortBy('order_sequence')->pluck('option_text');
            case 'matching':
                return $question->options->pluck('pair_text', 'option_text');
            default:
                return null;
        }
    }

    /**
     * User's attempt history
     */
    public function history()
    {
        $attempts = QuizAttempt::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->with('quiz')
            ->latest()
            ->paginate(10);

        return view('quiz.history', compact('attempts'));
    }
}
