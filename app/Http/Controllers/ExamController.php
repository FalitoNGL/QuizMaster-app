<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display the exam interface
     */
    public function show(QuizAttempt $attempt)
    {
        // Verify ownership
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if already completed
        if ($attempt->status === 'completed') {
            return redirect()->route('result.show', $attempt);
        }

        // Check time expiry
        if ($attempt->isTimeExpired()) {
            return $this->autoSubmit($attempt);
        }

        $attempt->load(['quiz.questions.options', 'answers']);

        // Get questions with randomization and limit
        $quiz = $attempt->quiz;
        $questions = $quiz->questions;
        
        // Apply randomization if enabled
        if ($quiz->randomize_questions) {
            // Use attempt ID as seed for consistent order per attempt
            $questions = $questions->shuffle(spl_object_id($attempt));
        }
        
        // Apply question limit if set
        if ($quiz->question_limit && $quiz->question_limit > 0) {
            $questions = $questions->take($quiz->question_limit);
        }

        // Get answered question IDs
        $answeredIds = $attempt->answers->pluck('question_id')->toArray();

        return view('quiz.exam', [
            'attempt' => $attempt,
            'quiz' => $quiz,
            'questions' => $questions->values(),
            'answeredIds' => $answeredIds,
            'remainingTime' => $attempt->remaining_time,
        ]);
    }

    /**
     * Save answer for a question
     */
    public function saveAnswer(Request $request, QuizAttempt $attempt)
    {
        // Verify ownership and status
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Attempt already completed'], 400);
        }

        // Check time expiry
        if ($attempt->isTimeExpired()) {
            $this->autoSubmit($attempt);
            return response()->json(['redirect' => route('result.show', $attempt)]);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required',
        ]);

        // Update or create answer
        $answer = QuizAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'user_answer' => $validated['answer'],
            ]
        );

        // Evaluate immediately
        $answer->evaluate();

        return response()->json([
            'success' => true,
            'answered_count' => $attempt->answers()->count(),
        ]);
    }

    /**
     * Submit the entire exam
     */
    public function submit(Request $request, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($attempt->status === 'completed') {
            return redirect()->route('result.show', $attempt);
        }

        return $this->processSubmission($attempt);
    }

    /**
     * Auto-submit when time expires (called via AJAX)
     */
    public function autoSubmit(QuizAttempt $attempt)
    {
        return $this->processSubmission($attempt);
    }

    /**
     * Process final submission
     */
    private function processSubmission(QuizAttempt $attempt)
    {
        // Calculate final score
        $score = $attempt->calculateScore();

        // Update attempt status
        $attempt->update([
            'status' => 'completed',
            'end_time' => now(),
        ]);

        // Add XP to user (10% of score)
        $xpEarned = (int) ceil($score * 0.1);
        $user = auth()->user();
        $user->addXp($xpEarned);

        // Check and award achievements
        $achievementService = new AchievementService();
        $newAchievements = $achievementService->checkAndAward($user);

        return redirect()->route('result.show', $attempt)
            ->with('xp_earned', $xpEarned)
            ->with('new_achievements', $newAchievements);
    }

    /**
     * Get server time for timer sync
     */
    public function getTime(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'remaining' => $attempt->remaining_time,
            'expired' => $attempt->isTimeExpired(),
        ]);
    }

    /**
     * Record a violation during exam
     */
    public function recordViolation(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Exam already completed'], 400);
        }

        $attempt->increment('violations');

        return response()->json([
            'success' => true,
            'violations' => $attempt->violations,
        ]);
    }
}
