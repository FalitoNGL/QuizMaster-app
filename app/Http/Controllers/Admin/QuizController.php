<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuizRequest;
use App\Http\Requests\Admin\UpdateQuizRequest;
use App\Models\Quiz;
use App\Models\Category;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    /**
     * Display a listing of quizzes
     */
    public function index()
    {
        $quizzes = Quiz::with('category')
            ->withCount('questions')
            ->latest()
            ->paginate(10);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new quiz
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.quizzes.create', compact('categories'));
    }

    /**
     * Store a newly created quiz
     */
    public function store(StoreQuizRequest $request)
    {
        $validated = $request->validated();

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('quizzes', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['randomize_questions'] = $request->boolean('randomize_questions');

        Quiz::create($validated);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz berhasil dibuat!');
    }

    /**
     * Display the specified quiz with its questions
     */
    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('admin.quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the quiz
     */
    public function edit(Quiz $quiz)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.quizzes.edit', compact('quiz', 'categories'));
    }

    /**
     * Update the specified quiz
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz)
    {
        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('quizzes', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_active'] = $request->boolean('is_active');

        // Handle empty dates (set to null)
        $validated['starts_at'] = $request->starts_at ?: null;
        $validated['ends_at'] = $request->ends_at ?: null;

        $quiz->update($validated);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz berhasil diperbarui!');
    }

    /**
     * Remove the specified quiz
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz berhasil dihapus!');
    }
}
