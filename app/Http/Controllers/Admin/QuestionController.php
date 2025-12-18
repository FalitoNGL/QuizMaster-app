<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionRequest;
use App\Http\Requests\Admin\UpdateQuestionRequest;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions for a quiz
     */
    public function index(Quiz $quiz)
    {
        $questions = $quiz->questions()->with('options')->paginate(20);
        return view('admin.questions.index', compact('quiz', 'questions'));
    }

    /**
     * Show the form for creating a new question
     */
    public function create(Quiz $quiz)
    {
        return view('admin.questions.create', compact('quiz'));
    }

    /**
     * Store a newly created question with options
     */
    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'type' => 'required|in:single_choice,multiple_choice,ordering,matching',
            'points' => 'required|integer|min:1|max:100',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'boolean',
            'options.*.pair_text' => 'nullable|string',
            'options.*.order_sequence' => 'nullable|integer',
        ]);

        // Handle media upload
        $mediaUrl = null;
        if ($request->hasFile('media')) {
            $mediaUrl = $request->file('media')->store('questions', 'public');
        }

        $question = $quiz->questions()->create([
            'content' => $validated['content'],
            'type' => $validated['type'],
            'media_url' => $mediaUrl,
            'points' => $validated['points'],
        ]);

        // Create options
        foreach ($validated['options'] as $index => $optionData) {
            $question->options()->create([
                'option_text' => $optionData['text'],
                'is_correct' => $optionData['is_correct'] ?? false,
                'pair_text' => $optionData['pair_text'] ?? null,
                'order_sequence' => $optionData['order_sequence'] ?? ($index + 1),
            ]);
        }

        return redirect()->route('admin.quizzes.questions.index', $quiz)
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    /**
     * Display the specified question
     */
    public function show(Quiz $quiz, Question $question)
    {
        $question->load('options');
        return view('admin.questions.show', compact('quiz', 'question'));
    }

    /**
     * Show the form for editing the question
     */
    public function edit(Quiz $quiz, Question $question)
    {
        $question->load('options');
        return view('admin.questions.edit', compact('quiz', 'question'));
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, Quiz $quiz, Question $question)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'type' => 'required|in:single_choice,multiple_choice,ordering,matching',
            'points' => 'required|integer|min:1|max:100',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'boolean',
        ]);

        // Handle media upload (replace old file)
        if ($request->hasFile('media')) {
            // Delete old media if exists
            if ($question->media_url) {
                Storage::disk('public')->delete($question->media_url);
            }
            $validated['media_url'] = $request->file('media')->store('questions', 'public');
        }

        $question->update([
            'content' => $validated['content'],
            'type' => $validated['type'],
            'media_url' => $validated['media_url'] ?? $question->media_url,
            'points' => $validated['points'],
        ]);

        // Sync options - delete old ones and create new
        $question->options()->delete();
        
        foreach ($validated['options'] as $index => $optionData) {
            $question->options()->create([
                'option_text' => $optionData['text'],
                'is_correct' => $optionData['is_correct'] ?? false,
                'order_sequence' => $index + 1,
            ]);
        }

        return redirect()->route('admin.quizzes.questions.index', $quiz)
            ->with('success', 'Soal berhasil diperbarui!');
    }

    /**
     * Remove the specified question
     * Note: File cleanup is handled by Question model's deleting event
     */
    public function destroy(Quiz $quiz, Question $question)
    {
        $question->delete();

        return redirect()->route('admin.quizzes.questions.index', $quiz)
            ->with('success', 'Soal berhasil dihapus!');
    }
}
