<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;

class BulkQuestionController extends Controller
{
    /**
     * Show bulk editor for quiz questions
     */
    public function index(Quiz $quiz)
    {
        $questions = $quiz->questions()->with('options')->paginate(50);
        $allQuizzes = Quiz::where('id', '!=', $quiz->id)->orderBy('title')->get();
        
        return view('admin.questions.bulk', [
            'quiz' => $quiz,
            'questions' => $questions,
            'allQuizzes' => $allQuizzes,
        ]);
    }

    /**
     * Bulk delete selected questions
     */
    public function delete(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $count = Question::whereIn('id', $validated['question_ids'])
            ->where('quiz_id', $quiz->id)
            ->delete();

        return redirect()->back()
            ->with('success', "{$count} soal berhasil dihapus!");
    }

    /**
     * Bulk move questions to another quiz
     */
    public function move(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
            'target_quiz_id' => 'required|exists:quizzes,id',
        ]);

        $count = Question::whereIn('id', $validated['question_ids'])
            ->where('quiz_id', $quiz->id)
            ->update(['quiz_id' => $validated['target_quiz_id']]);

        $targetQuiz = Quiz::find($validated['target_quiz_id']);

        return redirect()->back()
            ->with('success', "{$count} soal dipindahkan ke {$targetQuiz->title}!");
    }

    /**
     * Bulk update points
     */
    public function updatePoints(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
            'points' => 'required|integer|min:1|max:100',
        ]);

        $count = Question::whereIn('id', $validated['question_ids'])
            ->where('quiz_id', $quiz->id)
            ->update(['points' => $validated['points']]);

        return redirect()->back()
            ->with('success', "{$count} soal diupdate ke {$validated['points']} poin!");
    }
}
