<?php

namespace App\Imports;

use App\Models\Quiz;
use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class QuestionsImport implements ToCollection, WithHeadingRow
{
    protected $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    /**
     * Process Excel rows and create questions with options
     * 
     * Expected Excel format:
     * | content | type | points | option_1 | option_2 | option_3 | option_4 | correct_options | pair_1 | pair_2 | pair_3 | pair_4 |
     * 
     * For single_choice: correct_options = "1" (option number)
     * For multiple_choice: correct_options = "1,3" (comma-separated)
     * For ordering: correct_options = "2,1,4,3" (correct order of options)
     * For matching: pair_1, pair_2, etc. contain matching pairs
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip empty rows
            if (empty($row['content'])) {
                continue;
            }

            // Create question
            $question = $this->quiz->questions()->create([
                'content' => $row['content'],
                'type' => $row['type'] ?? 'single_choice',
                'points' => $row['points'] ?? 10,
                'media_url' => $row['media_url'] ?? null,
            ]);

            // Parse options (support up to 6 options)
            $options = [];
            for ($i = 1; $i <= 6; $i++) {
                $optKey = "option_{$i}";
                if (!empty($row[$optKey])) {
                    $options[$i] = $row[$optKey];
                }
            }

            // Parse correct options
            $correctOptions = [];
            if (!empty($row['correct_options'])) {
                $correctOptions = array_map('intval', explode(',', $row['correct_options']));
            }

            // Create options based on question type
            foreach ($options as $index => $optionText) {
                $optionData = [
                    'option_text' => $optionText,
                    'is_correct' => in_array($index, $correctOptions),
                ];

                // For ordering type
                if ($row['type'] === 'ordering') {
                    $orderPos = array_search($index, $correctOptions);
                    $optionData['order_sequence'] = $orderPos !== false ? $orderPos + 1 : $index;
                }

                // For matching type
                if ($row['type'] === 'matching') {
                    $pairKey = "pair_{$index}";
                    $optionData['pair_text'] = $row[$pairKey] ?? '';
                    $optionData['is_correct'] = true; // All matching options are "correct" pairs
                }

                $question->options()->create($optionData);
            }
        }
    }
}
