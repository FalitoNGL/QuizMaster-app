<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\User;
use App\Models\QuizAttempt;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with statistics
     */
    public function index()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_quizzes' => Quiz::count(),
            'total_questions' => Question::count(),
            'total_attempts' => QuizAttempt::count(),
            'completed_attempts' => QuizAttempt::where('status', 'completed')->count(),
        ];

        // Most active users (top 5)
        $activeUsers = User::withCount('quizAttempts')
            ->orderByDesc('quiz_attempts_count')
            ->limit(5)
            ->get();

        // Recent attempts
        $recentAttempts = QuizAttempt::with(['user', 'quiz'])
            ->latest()
            ->limit(10)
            ->get();

        // Quiz with most questions
        $popularQuizzes = Quiz::withCount('questions')
            ->orderByDesc('questions_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'activeUsers', 'recentAttempts', 'popularQuizzes'));
    }
}
