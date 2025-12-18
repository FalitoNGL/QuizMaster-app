<?php

namespace App\Console\Commands;

use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportQuestionsFromJson extends Command
{
    protected $signature = 'import:questions {--all : Import all JSON files} {--fresh : Delete existing questions first}';
    protected $description = 'Import questions from JSON files in storage/app/imports (skips duplicates)';

    public function handle()
    {
        $importPath = storage_path('app/imports');
        
        if (!File::isDirectory($importPath)) {
            $this->error('Folder imports tidak ditemukan!');
            return 1;
        }

        $files = File::glob($importPath . '/*.json');
        
        if (empty($files)) {
            $this->error('Tidak ada file JSON di folder imports!');
            return 1;
        }

        $this->info('Ditemukan ' . count($files) . ' file JSON.');
        $this->newLine();

        // Fresh import - delete existing questions
        if ($this->option('fresh')) {
            $this->warn('Mode --fresh: Menghapus semua soal yang ada...');
            Question::query()->each(function ($q) {
                $q->options()->delete();
                $q->delete();
            });
            $this->info('Semua soal dihapus.');
            $this->newLine();
        }

        $totalImported = 0;
        $totalSkipped = 0;
        $typeCounts = ['single_choice' => 0, 'multiple_choice' => 0, 'ordering' => 0, 'matching' => 0];

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $slug = $filename;
            
            $quiz = Quiz::where('slug', $slug)->first();
            
            if (!$quiz) {
                $this->warn("Quiz dengan slug '{$slug}' tidak ditemukan. Skip: {$filename}.json");
                continue;
            }

            $this->info("Importing: {$filename}.json → {$quiz->title}");

            $jsonContent = File::get($file);
            $questions = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("  Error parsing JSON: " . json_last_error_msg());
                continue;
            }

            $count = 0;
            $skipped = 0;

            foreach ($questions as $item) {
                $questionContent = $item['question'] ?? '';
                
                if (empty(trim($questionContent))) {
                    continue;
                }

                // Check for duplicate
                $exists = Question::where('quiz_id', $quiz->id)
                    ->where('content', $questionContent)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Map type
                $jsonType = $item['type'] ?? 'single';
                $type = match($jsonType) {
                    'single' => 'single_choice',
                    'multiple' => 'multiple_choice',
                    'ordering' => 'ordering',
                    'matching' => 'matching',
                    default => 'single_choice'
                };

                // Create question
                $question = $quiz->questions()->create([
                    'content' => $questionContent,
                    'type' => $type,
                    'points' => 10,
                    'explanation' => $item['explanation'] ?? null,
                    'reference' => $item['reference'] ?? null,
                ]);

                // Create options based on type
                switch ($type) {
                    case 'single_choice':
                        $this->createSingleChoiceOptions($question, $item);
                        break;
                    
                    case 'multiple_choice':
                        $this->createMultipleChoiceOptions($question, $item);
                        break;
                    
                    case 'ordering':
                        $this->createOrderingOptions($question, $item);
                        break;
                    
                    case 'matching':
                        $this->createMatchingOptions($question, $item);
                        break;
                }

                $typeCounts[$type]++;
                $count++;
            }

            $this->info("  ✓ {$count} soal diimport, {$skipped} duplikat di-skip");
            $totalImported += $count;
            $totalSkipped += $skipped;
        }

        $this->newLine();
        $this->info("=================================");
        $this->info("Total: {$totalImported} soal diimport");
        $this->info("       {$totalSkipped} duplikat di-skip");
        $this->newLine();
        $this->info("Detail per tipe:");
        $this->info("  - Single Choice  : {$typeCounts['single_choice']}");
        $this->info("  - Multiple Choice: {$typeCounts['multiple_choice']}");
        $this->info("  - Ordering       : {$typeCounts['ordering']}");
        $this->info("  - Matching       : {$typeCounts['matching']}");
        
        return 0;
    }

    private function createSingleChoiceOptions(Question $question, array $item): void
    {
        if (!isset($item['options']) || !is_array($item['options'])) {
            return;
        }
        
        $correctIndex = (int) ($item['correct'] ?? 0);
        
        foreach ($item['options'] as $index => $optionText) {
            $question->options()->create([
                'option_text' => $optionText,
                'is_correct' => ($index === $correctIndex),
            ]);
        }
    }

    private function createMultipleChoiceOptions(Question $question, array $item): void
    {
        if (!isset($item['options']) || !is_array($item['options'])) {
            return;
        }
        
        // correct HARUS berupa array untuk multiple choice, misal [0, 2]
        $correctIndices = $item['correct'] ?? [];
        
        // Jika bukan array (fallback), jadikan array
        if (!is_array($correctIndices)) {
            $correctIndices = [(int) $correctIndices];
        }
        
        // Pastikan semua nilai adalah integer untuk perbandingan
        $correctIndices = array_map('intval', $correctIndices);
        
        foreach ($item['options'] as $index => $optionText) {
            $isCorrect = in_array((int) $index, $correctIndices, true);
            $question->options()->create([
                'option_text' => $optionText,
                'is_correct' => $isCorrect,
            ]);
        }
    }

    private function createOrderingOptions(Question $question, array $item): void
    {
        // Ordering uses 'items' in JSON format
        $items = $item['items'] ?? $item['options'] ?? null;
        
        if (!$items || !is_array($items)) {
            return;
        }
        
        // Items sudah dalam urutan yang benar (sequence = index + 1)
        foreach ($items as $index => $optionText) {
            $question->options()->create([
                'option_text' => $optionText,
                'is_correct' => true,
                'order_sequence' => $index + 1,
            ]);
        }
    }

    private function createMatchingOptions(Question $question, array $item): void
    {
        if (!isset($item['pairs']) || !is_array($item['pairs'])) {
            return;
        }
        
        foreach ($item['pairs'] as $pair) {
            $question->options()->create([
                'option_text' => $pair['left'] ?? '',
                'pair_text' => $pair['right'] ?? '',
                'is_correct' => true,
            ]);
        }
    }
}
