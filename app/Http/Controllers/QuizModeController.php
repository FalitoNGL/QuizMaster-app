<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;

class QuizModeController extends Controller
{
    /**
     * Start Quiz Mode (instant feedback mode)
     */
    public function start(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_count' => 'required|integer|min:1|max:' . $quiz->questions()->count(),
            'time_limit' => 'required|integer|min:0|max:120',
        ]);

        // Check if quiz is available
        if (!$quiz->is_active) {
            return redirect()->route('quiz.lobby')
                ->with('error', 'Quiz tidak aktif');
        }

        // Get random questions based on user's choice
        $questions = $quiz->questions()
            ->with('options')
            ->inRandomOrder()
            ->take($validated['question_count'])
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('quiz.lobby')
                ->with('error', 'Quiz belum memiliki soal.');
        }

        // Store session data for quiz mode
        $sessionKey = 'quiz_mode_' . $quiz->id;
        session([
            $sessionKey => [
                'questions' => $questions->pluck('id')->toArray(),
                'current_index' => 0,
                'answers' => [],
                'correct_count' => 0,
                'time_limit' => $validated['time_limit'],
                'started_at' => now()->timestamp,
            ]
        ]);

        return redirect()->route('quiz.quiz-mode', $quiz);
    }

    /**
     * Show current question in Quiz Mode
     */
    public function show(Quiz $quiz)
    {
        $sessionKey = 'quiz_mode_' . $quiz->id;
        $session = session($sessionKey);

        if (!$session) {
            return redirect()->route('quiz.show', $quiz)
                ->with('error', 'Sesi kuis tidak ditemukan. Silakan mulai ulang.');
        }

        $currentIndex = $session['current_index'];
        $questionIds = $session['questions'];

        // Check if finished
        if ($currentIndex >= count($questionIds)) {
            return $this->showResult($quiz);
        }

        $question = \App\Models\Question::with('options')
            ->find($questionIds[$currentIndex]);

        // Calculate remaining time
        $remainingTime = null;
        if ($session['time_limit'] > 0) {
            $elapsed = now()->timestamp - $session['started_at'];
            $remainingTime = max(0, ($session['time_limit'] * 60) - $elapsed);
            
            // Auto finish if time is up
            if ($remainingTime <= 0) {
                return $this->showResult($quiz);
            }
        }

        return view('quiz.quiz-mode', [
            'quiz' => $quiz,
            'question' => $question,
            'currentIndex' => $currentIndex,
            'totalQuestions' => count($questionIds),
            'correctCount' => $session['correct_count'],
            'remainingTime' => $remainingTime,
            'answered' => $session['answers'][$question->id] ?? null,
        ]);
    }

    /**
     * Answer a question in Quiz Mode
     */
    public function answer(Request $request, Quiz $quiz)
    {
        $sessionKey = 'quiz_mode_' . $quiz->id;
        $session = session($sessionKey);

        if (!$session) {
            return response()->json(['error' => 'Session expired'], 400);
        }

        $currentIndex = $session['current_index'];
        $questionId = $session['questions'][$currentIndex];
        $question = \App\Models\Question::with('options')->find($questionId);

        $userAnswer = $request->input('answer');
        $isCorrect = $question->checkAnswer($userAnswer);

        // Update session
        $session['answers'][$questionId] = [
            'answer' => $userAnswer,
            'is_correct' => $isCorrect,
        ];
        if ($isCorrect) {
            $session['correct_count']++;
        }
        session([$sessionKey => $session]);

        // Get correct answer for feedback
        $correctOption = $question->correctOptions()->first();

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_option_id' => $correctOption?->id,
            'explanation' => $question->explanation ?? null,
            'reference' => $question->reference ?? null,
        ]);
    }

    /**
     * Move to next question
     */
    public function next(Quiz $quiz)
    {
        $sessionKey = 'quiz_mode_' . $quiz->id;
        $session = session($sessionKey);

        if (!$session) {
            return redirect()->route('quiz.show', $quiz);
        }

        $session['current_index']++;
        session([$sessionKey => $session]);

        return redirect()->route('quiz.quiz-mode', $quiz);
    }

    /**
     * Show quiz mode result
     */
    private function showResult(Quiz $quiz)
    {
        $sessionKey = 'quiz_mode_' . $quiz->id;
        $session = session($sessionKey);

        $totalQuestions = count($session['questions']);
        $correctCount = $session['correct_count'];
        $percentage = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        // Clear session
        session()->forget($sessionKey);

        return view('quiz.quiz-mode-result', [
            'quiz' => $quiz,
            'totalQuestions' => $totalQuestions,
            'correctCount' => $correctCount,
            'percentage' => $percentage,
        ]);
    }

    /**
     * Finish quiz mode early
     */
    public function finish(Quiz $quiz)
    {
        return $this->showResult($quiz);
    }
}
