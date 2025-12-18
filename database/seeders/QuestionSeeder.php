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
        $quizQuestions = [
            'etos-sandi-iii' => [
                [
                    'question_text' => 'Apa kepanjangan dari AES dalam kriptografi?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'AES adalah Advanced Encryption Standard, algoritma enkripsi simetris yang banyak digunakan.',
                    'options' => [
                        ['text' => 'Advanced Encryption Standard', 'is_correct' => true],
                        ['text' => 'American Encryption System', 'is_correct' => false],
                        ['text' => 'Automatic Encoding Standard', 'is_correct' => false],
                        ['text' => 'Advanced Encoding System', 'is_correct' => false],
                    ]
                ],
                [
                    'question_text' => 'Manakah yang termasuk algoritma hash?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'SHA-256 adalah algoritma hash yang menghasilkan output 256-bit.',
                    'options' => [
                        ['text' => 'RSA', 'is_correct' => false],
                        ['text' => 'SHA-256', 'is_correct' => true],
                        ['text' => 'AES', 'is_correct' => false],
                        ['text' => 'DES', 'is_correct' => false],
                    ]
                ],
                [
                    'question_text' => 'Berapa panjang kunci minimum untuk AES?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'AES mendukung kunci 128, 192, dan 256 bit. Minimum adalah 128 bit.',
                    'options' => [
                        ['text' => '64 bit', 'is_correct' => false],
                        ['text' => '128 bit', 'is_correct' => true],
                        ['text' => '256 bit', 'is_correct' => false],
                        ['text' => '512 bit', 'is_correct' => false],
                    ]
                ],
            ],
            'pemrograman-jaringan' => [
                [
                    'question_text' => 'Port default untuk HTTP adalah?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'HTTP menggunakan port 80 secara default.',
                    'options' => [
                        ['text' => '21', 'is_correct' => false],
                        ['text' => '22', 'is_correct' => false],
                        ['text' => '80', 'is_correct' => true],
                        ['text' => '443', 'is_correct' => false],
                    ]
                ],
                [
                    'question_text' => 'Protokol mana yang connectionless?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'UDP adalah protokol connectionless, tidak memerlukan handshake.',
                    'options' => [
                        ['text' => 'TCP', 'is_correct' => false],
                        ['text' => 'UDP', 'is_correct' => true],
                        ['text' => 'HTTP', 'is_correct' => false],
                        ['text' => 'FTP', 'is_correct' => false],
                    ]
                ],
                [
                    'question_text' => 'Layer mana yang bertanggung jawab untuk routing?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'Network Layer (Layer 3) bertanggung jawab untuk routing paket.',
                    'options' => [
                        ['text' => 'Physical Layer', 'is_correct' => false],
                        ['text' => 'Data Link Layer', 'is_correct' => false],
                        ['text' => 'Network Layer', 'is_correct' => true],
                        ['text' => 'Transport Layer', 'is_correct' => false],
                    ]
                ],
            ],
            'kriptografi-terapan' => [
                [
                    'question_text' => 'RSA termasuk jenis enkripsi?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'RSA adalah algoritma enkripsi asimetris menggunakan public dan private key.',
                    'options' => [
                        ['text' => 'Simetris', 'is_correct' => false],
                        ['text' => 'Asimetris', 'is_correct' => true],
                        ['text' => 'Hash', 'is_correct' => false],
                        ['text' => 'Hybrid', 'is_correct' => false],
                    ]
                ],
                [
                    'question_text' => 'Fungsi hash yang menghasilkan output 160 bit adalah?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'SHA-1 menghasilkan hash 160-bit (20 byte).',
                    'options' => [
                        ['text' => 'MD5', 'is_correct' => false],
                        ['text' => 'SHA-1', 'is_correct' => true],
                        ['text' => 'SHA-256', 'is_correct' => false],
                        ['text' => 'SHA-512', 'is_correct' => false],
                    ]
                ],
            ],
            'pemrograman-lanjutan' => [
                [
                    'question_text' => 'Design pattern yang memastikan hanya ada satu instance?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'Singleton pattern memastikan sebuah class hanya memiliki satu instance.',
                    'options' => [
                        ['text' => 'Factory', 'is_correct' => false],
                        ['text' => 'Singleton', 'is_correct' => true],
                        ['text' => 'Observer', 'is_correct' => false],
                        ['text' => 'Strategy', 'is_correct' => false],
                    ]
                ],
                [
                    'question_text' => 'Prinsip SOLID, huruf S adalah?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'S dalam SOLID adalah Single Responsibility Principle.',
                    'options' => [
                        ['text' => 'Single Responsibility', 'is_correct' => true],
                        ['text' => 'Substitution', 'is_correct' => false],
                        ['text' => 'Segregation', 'is_correct' => false],
                        ['text' => 'Security', 'is_correct' => false],
                    ]
                ],
            ],
            'sistem-operasi-virtualisasi' => [
                [
                    'question_text' => 'Hypervisor Type 1 disebut juga?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'Type 1 hypervisor berjalan langsung di hardware (bare-metal).',
                    'options' => [
                        ['text' => 'Hosted', 'is_correct' => false],
                        ['text' => 'Bare-metal', 'is_correct' => true],
                        ['text' => 'Container', 'is_correct' => false],
                        ['text' => 'Emulator', 'is_correct' => false],
                    ]
                ],
                [
                    'question_text' => 'Docker menggunakan teknologi?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'Docker menggunakan containerization untuk isolasi aplikasi.',
                    'options' => [
                        ['text' => 'Virtual Machine', 'is_correct' => false],
                        ['text' => 'Container', 'is_correct' => true],
                        ['text' => 'Emulation', 'is_correct' => false],
                        ['text' => 'Simulation', 'is_correct' => false],
                    ]
                ],
            ],
            'sistem-telekomunikasi' => [
                [
                    'question_text' => 'Frekuensi WiFi 2.4 GHz memiliki berapa channel?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'WiFi 2.4 GHz memiliki 14 channel (11 di US, 13 di EU, 14 di Japan).',
                    'options' => [
                        ['text' => '11', 'is_correct' => false],
                        ['text' => '13', 'is_correct' => false],
                        ['text' => '14', 'is_correct' => true],
                        ['text' => '20', 'is_correct' => false],
                    ]
                ],
                [
                    'question_text' => 'LTE adalah teknologi generasi?',
                    'type' => 'single',
                    'points' => 10,
                    'explanation' => 'LTE (Long Term Evolution) adalah teknologi jaringan seluler 4G.',
                    'options' => [
                        ['text' => '3G', 'is_correct' => false],
                        ['text' => '4G', 'is_correct' => true],
                        ['text' => '5G', 'is_correct' => false],
                        ['text' => '2G', 'is_correct' => false],
                    ]
                ],
            ],
        ];

        foreach ($quizQuestions as $quizSlug => $questions) {
            $quiz = Quiz::where('slug', $quizSlug)->first();
            
            if (!$quiz) continue;

            foreach ($questions as $questionData) {
                // Check if question already exists
                $existingQuestion = Question::where('quiz_id', $quiz->id)
                    ->where('question_text', $questionData['question_text'])
                    ->first();
                
                if ($existingQuestion) continue;

                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['question_text'],
                    'type' => $questionData['type'],
                    'points' => $questionData['points'],
                    'explanation' => $questionData['explanation'] ?? null,
                ]);

                foreach ($questionData['options'] as $optionData) {
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'],
                    ]);
                }
            }
        }

        $this->command->info('Sample questions berhasil dibuat!');
    }
}
