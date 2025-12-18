<?php

namespace App\Services;

use App\Models\User;
use App\Models\Achievement;
use App\Models\QuizAttempt;

class AchievementService
{
    /**
     * Check and award achievements to user
     */
    public function checkAndAward(User $user): array
    {
        $newAchievements = [];
        $achievements = Achievement::all();

        foreach ($achievements as $achievement) {
            // Skip if already earned
            if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }

            // Check criteria
            if ($this->checkCriteria($user, $achievement)) {
                $this->awardAchievement($user, $achievement);
                $newAchievements[] = $achievement;
            }
        }

        return $newAchievements;
    }

    /**
     * Check if user meets achievement criteria
     */
    private function checkCriteria(User $user, Achievement $achievement): bool
    {
        switch ($achievement->criteria_type) {
            case 'quiz_count':
                // Complete N quizzes
                $count = QuizAttempt::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->count();
                return $count >= $achievement->criteria_value;

            case 'perfect_score':
                // Get 100% on any quiz
                $perfectCount = QuizAttempt::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->whereColumn('total_score', 'max_score')
                    ->where('max_score', '>', 0)
                    ->count();
                return $perfectCount >= $achievement->criteria_value;

            case 'speed_completion':
                // Complete quiz in under X% of allotted time
                $speedCount = 0;
                $attempts = QuizAttempt::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->whereNotNull('end_time')
                    ->with('quiz')
                    ->get();
                    
                foreach ($attempts as $attempt) {
                    if (!$attempt->quiz || !$attempt->end_time || !$attempt->start_time) continue;
                    
                    $allowedTime = $attempt->quiz->time_limit * 60; // in seconds
                    $usedTime = $attempt->start_time->diffInSeconds($attempt->end_time);
                    
                    if ($usedTime < ($allowedTime * 0.5)) { // Under 50%
                        $speedCount++;
                    }
                }
                return $speedCount >= $achievement->criteria_value;

            case 'total_xp':
                // Reach N total XP
                return $user->xp >= $achievement->criteria_value;

            case 'level':
                // Reach level N
                return $user->level >= $achievement->criteria_value;

            default:
                return false;
        }
    }

    /**
     * Award achievement to user
     */
    private function awardAchievement(User $user, Achievement $achievement): void
    {
        $user->achievements()->attach($achievement->id, [
            'earned_at' => now(),
        ]);

        // Give XP reward
        if ($achievement->xp_reward > 0) {
            $user->addXp($achievement->xp_reward);
        }
    }
}
