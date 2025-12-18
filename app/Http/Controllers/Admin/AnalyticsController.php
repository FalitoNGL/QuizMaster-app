<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Show analytics for a specific quiz
     */
    public function show(Quiz $quiz)
    {
        $quiz->loadCount('questions');
        
        // Get all completed attempts
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('status', 'completed')
            ->with('user')
            ->get();

        // Score distribution
        $scoreRanges = [
            '0-20' => 0,
            '21-40' => 0,
            '41-60' => 0,
            '61-80' => 0,
            '81-100' => 0,
        ];

        foreach ($attempts as $attempt) {
            $percentage = $attempt->max_score > 0 
                ? ($attempt->total_score / $attempt->max_score) * 100 
                : 0;
            
            if ($percentage <= 20) $scoreRanges['0-20']++;
            elseif ($percentage <= 40) $scoreRanges['21-40']++;
            elseif ($percentage <= 60) $scoreRanges['41-60']++;
            elseif ($percentage <= 80) $scoreRanges['61-80']++;
            else $scoreRanges['81-100']++;
        }

        // Average score
        $avgScore = $attempts->count() > 0 
            ? $attempts->avg(function($a) {
                return $a->max_score > 0 ? ($a->total_score / $a->max_score) * 100 : 0;
            }) 
            : 0;

        // Average completion time
        $avgTime = $attempts->avg(function($a) {
            return $a->start_time && $a->end_time 
                ? $a->start_time->diffInMinutes($a->end_time) 
                : 0;
        });

        // Hardest questions (lowest correct rate)
        $hardestQuestions = $this->getHardestQuestions($quiz);

        // Completion rate
        $completionRate = QuizAttempt::where('quiz_id', $quiz->id)->count() > 0
            ? ($attempts->count() / QuizAttempt::where('quiz_id', $quiz->id)->count()) * 100
            : 0;

        // Recent attempts
        $recentAttempts = $attempts->sortByDesc('created_at')->take(10);

        return view('admin.analytics.show', [
            'quiz' => $quiz,
            'attempts' => $attempts,
            'scoreRanges' => $scoreRanges,
            'avgScore' => round($avgScore, 1),
            'avgTime' => round($avgTime, 1),
            'hardestQuestions' => $hardestQuestions,
            'completionRate' => round($completionRate, 1),
            'recentAttempts' => $recentAttempts,
        ]);
    }

    /**
     * Get questions with lowest correct answer rate
     */
    private function getHardestQuestions(Quiz $quiz)
    {
        $questions = $quiz->questions()->with('options')->get();
        $questionStats = [];

        foreach ($questions as $question) {
            $totalAnswers = QuizAnswer::where('question_id', $question->id)->count();
            $correctAnswers = QuizAnswer::where('question_id', $question->id)
                ->where('is_correct', true)
                ->count();

            $correctRate = $totalAnswers > 0 
                ? ($correctAnswers / $totalAnswers) * 100 
                : 0;

            $questionStats[] = [
                'question' => $question,
                'total_answers' => $totalAnswers,
                'correct_answers' => $correctAnswers,
                'correct_rate' => round($correctRate, 1),
            ];
        }

        // Sort by correct rate (ascending = hardest first)
        usort($questionStats, fn($a, $b) => $a['correct_rate'] <=> $b['correct_rate']);

        return array_slice($questionStats, 0, 5);
    }
}
