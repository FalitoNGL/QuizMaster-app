<?php

namespace App\Console\Commands;

use App\Models\Question;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateQuestions extends Command
{
    protected $signature = 'questions:remove-duplicates';
    protected $description = 'Remove duplicate questions (keep first, delete rest)';

    public function handle()
    {
        $this->info('Mencari soal duplikat...');

        // Find duplicates by quiz_id and content
        $duplicates = DB::table('questions')
            ->select('quiz_id', 'content', DB::raw('COUNT(*) as cnt'), DB::raw('MIN(id) as keep_id'))
            ->groupBy('quiz_id', 'content')
            ->having('cnt', '>', 1)
            ->get();

        $this->info("Ditemukan {$duplicates->count()} grup soal duplikat.");

        $totalDeleted = 0;

        foreach ($duplicates as $dup) {
            // Delete all except the first one (keep_id)
            $toDelete = Question::where('quiz_id', $dup->quiz_id)
                ->where('content', $dup->content)
                ->where('id', '!=', $dup->keep_id)
                ->get();

            foreach ($toDelete as $question) {
                $question->options()->delete();
                $question->delete();
                $totalDeleted++;
            }
        }

        $this->info("✓ {$totalDeleted} soal duplikat berhasil dihapus!");

        // Show remaining count
        $remaining = Question::count();
        $this->info("Total soal sekarang: {$remaining}");

        return 0;
    }
}
