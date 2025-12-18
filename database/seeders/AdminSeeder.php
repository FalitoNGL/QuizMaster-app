<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@quizmaster.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'level' => 99,
                'xp' => 9999,
            ]
        );

        // Create demo user
        User::firstOrCreate(
            ['email' => 'user@quizmaster.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'level' => 1,
                'xp' => 0,
            ]
        );

        $this->command->info('Admin dan Demo User berhasil dibuat!');
        $this->command->info('Admin: admin@quizmaster.com / password123');
        $this->command->info('User: user@quizmaster.com / password123');
    }
}

