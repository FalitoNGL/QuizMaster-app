<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'First Steps',
                'slug' => 'first-steps',
                'icon' => '🎯',
                'description' => 'Selesaikan quiz pertama Anda',
                'criteria_type' => 'quiz_count',
                'criteria_value' => 1,
                'xp_reward' => 50,
            ],
            [
                'name' => 'Quiz Explorer',
                'slug' => 'quiz-explorer',
                'icon' => '🗺️',
                'description' => 'Selesaikan 5 quiz',
                'criteria_type' => 'quiz_count',
                'criteria_value' => 5,
                'xp_reward' => 100,
            ],
            [
                'name' => 'Quiz Master',
                'slug' => 'quiz-master',
                'icon' => '🏆',
                'description' => 'Selesaikan 10 quiz',
                'criteria_type' => 'quiz_count',
                'criteria_value' => 10,
                'xp_reward' => 200,
            ],
            [
                'name' => 'Quiz Legend',
                'slug' => 'quiz-legend',
                'icon' => '👑',
                'description' => 'Selesaikan 50 quiz',
                'criteria_type' => 'quiz_count',
                'criteria_value' => 50,
                'xp_reward' => 500,
            ],
            [
                'name' => 'Perfect Score',
                'slug' => 'perfect-score',
                'icon' => '💯',
                'description' => 'Dapatkan nilai 100% pada quiz',
                'criteria_type' => 'perfect_score',
                'criteria_value' => 1,
                'xp_reward' => 150,
            ],
            [
                'name' => 'Perfectionist',
                'slug' => 'perfectionist',
                'icon' => '✨',
                'description' => 'Dapatkan nilai 100% pada 5 quiz',
                'criteria_type' => 'perfect_score',
                'criteria_value' => 5,
                'xp_reward' => 300,
            ],
            [
                'name' => 'Speed Demon',
                'slug' => 'speed-demon',
                'icon' => '⚡',
                'description' => 'Selesaikan quiz dalam waktu kurang dari 50%',
                'criteria_type' => 'speed_completion',
                'criteria_value' => 1,
                'xp_reward' => 100,
            ],
            [
                'name' => 'Lightning Fast',
                'slug' => 'lightning-fast',
                'icon' => '🚀',
                'description' => 'Selesaikan 5 quiz dengan kecepatan super',
                'criteria_type' => 'speed_completion',
                'criteria_value' => 5,
                'xp_reward' => 250,
            ],
            [
                'name' => 'Rising Star',
                'slug' => 'rising-star',
                'icon' => '⭐',
                'description' => 'Mencapai level 5',
                'criteria_type' => 'level',
                'criteria_value' => 5,
                'xp_reward' => 100,
            ],
            [
                'name' => 'XP Hunter',
                'slug' => 'xp-hunter',
                'icon' => '💎',
                'description' => 'Kumpulkan 1000 XP',
                'criteria_type' => 'total_xp',
                'criteria_value' => 1000,
                'xp_reward' => 100,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                ['slug' => $achievement['slug']],
                $achievement
            );
        }
    }
}
