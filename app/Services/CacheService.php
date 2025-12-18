<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache durations in seconds
     */
    const QUIZ_LIST_TTL = 300;      // 5 minutes
    const LEADERBOARD_TTL = 60;     // 1 minute
    const QUIZ_DETAIL_TTL = 300;    // 5 minutes

    /**
     * Get cached active quizzes list
     */
    public static function getActiveQuizzes()
    {
        return Cache::remember('quizzes.active', self::QUIZ_LIST_TTL, function () {
            return Quiz::where('is_active', true)
                ->with('category')
                ->withCount('questions')
                ->get();
        });
    }

    /**
     * Get cached leaderboard
     */
    public static function getLeaderboard(int $limit = 100)
    {
        return Cache::remember("leaderboard.top{$limit}", self::LEADERBOARD_TTL, function () use ($limit) {
            return User::orderByDesc('xp')
                ->orderByDesc('level')
                ->take($limit)
                ->get(['id', 'name', 'avatar', 'xp', 'level']);
        });
    }

    /**
     * Get cached quiz with questions
     */
    public static function getQuizWithQuestions(int $quizId)
    {
        return Cache::remember("quiz.{$quizId}.full", self::QUIZ_DETAIL_TTL, function () use ($quizId) {
            return Quiz::with(['questions.options', 'category'])
                ->findOrFail($quizId);
        });
    }

    /**
     * Clear quiz-related cache
     */
    public static function clearQuizCache(int $quizId = null)
    {
        Cache::forget('quizzes.active');
        
        if ($quizId) {
            Cache::forget("quiz.{$quizId}.full");
        }
    }

    /**
     * Clear leaderboard cache
     */
    public static function clearLeaderboardCache()
    {
        Cache::forget('leaderboard.top100');
        Cache::forget('leaderboard.top10');
    }

    /**
     * Clear all app caches
     */
    public static function clearAll()
    {
        self::clearQuizCache();
        self::clearLeaderboardCache();
    }
}
