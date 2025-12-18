<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeaderboardController extends Controller
{
    /**
     * Display global leaderboard (optimized with caching)
     */
    public function index()
    {
        // Top users by XP (cached for 1 minute)
        $topUsers = Cache::remember('leaderboard.top100', 60, function () {
            return User::where('role', '!=', 'admin')
                ->orderByDesc('xp')
                ->orderByDesc('level')
                ->limit(100)
                ->get(['id', 'name', 'avatar', 'xp', 'level']);
        });

        // Current user rank
        $currentUserRank = null;
        if (auth()->check()) {
            $currentUserRank = Cache::remember(
                'user.rank.' . auth()->id(), 
                60, 
                function () {
                    return User::where('role', '!=', 'admin')
                        ->where('xp', '>', auth()->user()->xp)
                        ->count() + 1;
                }
            );
        }

        // Weekly top performers (cached for 5 minutes)
        $weeklyTop = Cache::remember('leaderboard.weekly', 300, function () {
            return User::where('role', '!=', 'admin')
                ->whereHas('quizAttempts', function ($q) {
                    $q->where('created_at', '>=', now()->subDays(7))
                      ->where('status', 'completed');
                })
                ->withSum(['quizAttempts' => function ($q) {
                    $q->where('created_at', '>=', now()->subDays(7))
                      ->where('status', 'completed');
                }], 'total_score')
                ->orderByDesc('quiz_attempts_sum_total_score')
                ->limit(10)
                ->get(['id', 'name', 'avatar', 'xp', 'level']);
        });

        // Stats (cached for 5 minutes)
        $stats = Cache::remember('leaderboard.stats', 300, function () {
            return [
                'total_users' => User::where('role', '!=', 'admin')->count(),
                'total_quizzes_completed' => QuizAttempt::where('status', 'completed')->count(),
                'total_xp_earned' => User::where('role', '!=', 'admin')->sum('xp'),
            ];
        });

        return view('leaderboard.index', compact('topUsers', 'currentUserRank', 'weeklyTop', 'stats'));
    }
}
