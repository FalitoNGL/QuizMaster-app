<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Imports\QuestionsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    /**
     * Show import form
     */
    public function showForm(Quiz $quiz)
    {
        return view('admin.import.form', compact('quiz'));
    }

    /**
     * Process Excel import
     */
    public function import(Request $request, Quiz $quiz)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new QuestionsImport($quiz), $request->file('file'));

            return redirect()->route('admin.quizzes.questions.index', $quiz)
                ->with('success', 'Soal berhasil diimport dari Excel!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        $templatePath = storage_path('app/templates/questions_template.xlsx');
        
        if (!file_exists($templatePath)) {
            // Create template directory if not exists
            if (!is_dir(storage_path('app/templates'))) {
                mkdir(storage_path('app/templates'), 0755, true);
            }
            
            // Generate simple template
            return $this->generateTemplate();
        }

        return response()->download($templatePath, 'template_import_soal.xlsx');
    }

    /**
     * Generate CSV template as fallback
     */
    private function generateTemplate()
    {
        $headers = [
            'content', 'type', 'points', 
            'option_1', 'option_2', 'option_3', 'option_4', 
            'correct_options',
            'pair_1', 'pair_2', 'pair_3', 'pair_4'
        ];

        $examples = [
            [
                'Apa ibu kota Indonesia?', 
                'single_choice', 
                10, 
                'Jakarta', 'Bandung', 'Surabaya', 'Medan',
                '1',
                '', '', '', ''
            ],
            [
                'Pilih bahasa pemrograman:', 
                'multiple_choice', 
                15, 
                'Python', 'HTML', 'Java', 'CSS',
                '1,3',
                '', '', '', ''
            ],
        ];

        $content = implode(',', $headers) . "\n";
        foreach ($examples as $row) {
            $content .= implode(',', array_map(function($val) {
                return '"' . str_replace('"', '""', $val) . '"';
            }, $row)) . "\n";
        }

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="template_import_soal.csv"');
    }
}
