<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizzes = Quiz::all();

        foreach ($quizzes as $quiz) {
            // Create 5 sample questions per quiz
            $questions = $this->getSampleQuestions($quiz->title);
            
            foreach ($questions as $index => $questionData) {
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['question'],
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => $questionData['explanation'] ?? null,
                ]);

                foreach ($questionData['options'] as $optIndex => $option) {
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'is_correct' => $option['correct'] ?? false,
                        'order_position' => $optIndex + 1,
                    ]);
                }
            }
        }

        $this->command->info('Sample questions created for all quizzes!');
    }

    private function getSampleQuestions(string $quizTitle): array
    {
        // General IT questions that work for any quiz
        $generalQuestions = [
            [
                'question' => 'Apa kepanjangan dari HTTP?',
                'explanation' => 'HTTP adalah singkatan dari HyperText Transfer Protocol, protokol dasar untuk komunikasi web.',
                'options' => [
                    ['text' => 'HyperText Transfer Protocol', 'correct' => true],
                    ['text' => 'High Text Transfer Protocol', 'correct' => false],
                    ['text' => 'HyperText Transmission Protocol', 'correct' => false],
                    ['text' => 'High Transfer Text Protocol', 'correct' => false],
                ],
            ],
            [
                'question' => 'Bahasa pemrograman apa yang digunakan untuk styling halaman web?',
                'explanation' => 'CSS (Cascading Style Sheets) digunakan untuk mengatur tampilan dan styling halaman web.',
                'options' => [
                    ['text' => 'HTML', 'correct' => false],
                    ['text' => 'CSS', 'correct' => true],
                    ['text' => 'JavaScript', 'correct' => false],
                    ['text' => 'PHP', 'correct' => false],
                ],
            ],
            [
                'question' => 'Apa fungsi utama dari database?',
                'explanation' => 'Database berfungsi untuk menyimpan, mengelola, dan mengambil data secara terstruktur.',
                'options' => [
                    ['text' => 'Menjalankan program', 'correct' => false],
                    ['text' => 'Menyimpan dan mengelola data', 'correct' => true],
                    ['text' => 'Menampilkan gambar', 'correct' => false],
                    ['text' => 'Mengakses internet', 'correct' => false],
                ],
            ],
            [
                'question' => 'Port default untuk HTTPS adalah?',
                'explanation' => 'HTTPS menggunakan port 443 secara default, sedangkan HTTP menggunakan port 80.',
                'options' => [
                    ['text' => '80', 'correct' => false],
                    ['text' => '443', 'correct' => true],
                    ['text' => '8080', 'correct' => false],
                    ['text' => '22', 'correct' => false],
                ],
            ],
            [
                'question' => 'Apa yang dimaksud dengan API?',
                'explanation' => 'API (Application Programming Interface) adalah antarmuka yang memungkinkan aplikasi berkomunikasi satu sama lain.',
                'options' => [
                    ['text' => 'Application Programming Interface', 'correct' => true],
                    ['text' => 'Application Process Integration', 'correct' => false],
                    ['text' => 'Automated Program Interface', 'correct' => false],
                    ['text' => 'Application Protocol Integration', 'correct' => false],
                ],
            ],
        ];

        return $generalQuestions;
    }
}
