<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizzes = [
            [
                'title' => 'Etos Sandi III',
                'slug' => 'etos-sandi-iii',
                'description' => 'Quiz tentang etika dan standar keamanan sandi.',
                'time_limit' => 30,
                'is_active' => true,
            ],
            [
                'title' => 'Pemrograman Jaringan',
                'slug' => 'pemrograman-jaringan',
                'description' => 'Quiz tentang pemrograman aplikasi berbasis jaringan.',
                'time_limit' => 30,
                'is_active' => true,
            ],
            [
                'title' => 'Kriptografi Terapan',
                'slug' => 'kriptografi-terapan',
                'description' => 'Quiz tentang implementasi algoritma kriptografi.',
                'time_limit' => 30,
                'is_active' => true,
            ],
            [
                'title' => 'Pemrograman Lanjutan',
                'slug' => 'pemrograman-lanjutan',
                'description' => 'Quiz tentang konsep pemrograman tingkat lanjut.',
                'time_limit' => 30,
                'is_active' => true,
            ],
            [
                'title' => 'Sistem Operasi & Virtualisasi',
                'slug' => 'sistem-operasi-virtualisasi',
                'description' => 'Quiz tentang sistem operasi dan teknologi virtualisasi.',
                'time_limit' => 30,
                'is_active' => true,
            ],
            [
                'title' => 'Sistem Telekomunikasi',
                'slug' => 'sistem-telekomunikasi',
                'description' => 'Quiz tentang sistem dan jaringan telekomunikasi.',
                'time_limit' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($quizzes as $quiz) {
            Quiz::firstOrCreate(
                ['slug' => $quiz['slug']],
                $quiz
            );
        }

        $this->command->info('6 Quizzes berhasil dibuat!');
    }
}
