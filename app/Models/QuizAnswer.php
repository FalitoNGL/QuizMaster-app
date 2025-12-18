<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'user_answer',
        'is_correct',
    ];

    protected $casts = [
        'user_answer' => 'array',
        'is_correct' => 'boolean',
    ];

    /**
     * Get parent attempt
     */
    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    /**
     * Get question
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Check and set if answer is correct
     */
    public function evaluate(): void
    {
        $this->is_correct = $this->question->checkAnswer($this->user_answer);
        $this->save();
    }
}
