<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display quiz lobby with progress
     * Note: Cache disabled for development. Enable CacheService::getActiveQuizzes() for production.
     */
    public function index()
    {
        // Direct query (no cache) - easier for development & demo
        // For production, use: $quizzes = CacheService::getActiveQuizzes();
        $quizzes = Quiz::where('is_active', true)
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $userId = auth()->id();
        
        // Batch load user progress for all quizzes at once
        $userAttempts = QuizAttempt::where('user_id', $userId)
            ->where('status', 'completed')
            ->select('quiz_id', DB::raw('COUNT(*) as attempts_count'), DB::raw('MAX(total_score) as best_score'))
            ->groupBy('quiz_id')
            ->get()
            ->keyBy('quiz_id');

        // Attach progress to each quiz
        $quizzes->each(function ($quiz) use ($userAttempts) {
            $progress = $userAttempts->get($quiz->id);
            $quiz->user_attempts = $progress?->attempts_count ?? 0;
            $quiz->has_completed = $quiz->user_attempts > 0;
            $quiz->best_score = $progress?->best_score ?? 0;
        });

        return view('quiz.lobby', compact('quizzes'));
    }

    /**
     * Show quiz detail before starting
     */
    public function show(Quiz $quiz)
    {
        if (!$quiz->is_active) {
            abort(404, 'Quiz tidak ditemukan');
        }

        $quiz->loadCount('questions');
        
        return view('quiz.show', compact('quiz'));
    }

    /**
     * Start a new quiz attempt
     */
    public function start(Quiz $quiz)
    {
        // Check if quiz is active
        if (!$quiz->is_active) {
            return redirect()->route('quiz.lobby')
                ->with('error', 'Quiz tidak aktif');
        }

        // Check schedule availability
        if (!$quiz->isAvailable()) {
            $message = $quiz->schedule_status === 'coming_soon' 
                ? 'Quiz belum dibuka. Mulai pada: ' . $quiz->starts_at->format('d M Y H:i')
                : 'Quiz sudah ditutup.';
            return redirect()->route('quiz.lobby')
                ->with('error', $message);
        }

        // Check if quiz has questions
        $questionsCount = $quiz->questions()->count();
        if ($questionsCount === 0) {
            return redirect()->route('quiz.lobby')
                ->with('error', 'Quiz belum memiliki soal.');
        }

        // Check for existing in-progress attempt
        $existingAttempt = QuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('exam.show', $existingAttempt);
        }

        // Calculate max score based on question limit
        $limit = $quiz->question_limit && $quiz->question_limit > 0 
            ? min($quiz->question_limit, $questionsCount) 
            : $questionsCount;
        
        $maxScore = $quiz->questions()
            ->orderByDesc('points')
            ->take($limit)
            ->sum('points');

        $attempt = QuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'start_time' => now(),
            'status' => 'in_progress',
            'max_score' => $maxScore,
        ]);

        return redirect()->route('exam.show', $attempt);
    }
}
