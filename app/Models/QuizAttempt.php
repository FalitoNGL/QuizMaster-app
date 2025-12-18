<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'start_time',
        'end_time',
        'total_score',
        'max_score',
        'status',
        'violations',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get user who took this attempt
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get quiz for this attempt
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get answers for this attempt
     */
    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id');
    }

    /**
     * Calculate and update score
     */
    public function calculateScore(): int
    {
        $score = 0;
        foreach ($this->answers as $answer) {
            if ($answer->is_correct) {
                $score += $answer->question->points;
            }
        }
        $this->total_score = $score;
        $this->save();
        return $score;
    }

    /**
     * Check if attempt is still in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if time limit exceeded
     */
    public function isTimeExpired(): bool
    {
        if (!$this->start_time) return false;
        
        $timeLimit = $this->quiz->time_limit; // minutes
        $endTime = $this->start_time->addMinutes($timeLimit);
        
        return now()->greaterThan($endTime);
    }

    /**
     * Get remaining time in seconds
     */
    public function getRemainingTimeAttribute(): int
    {
        if (!$this->start_time || !$this->isInProgress()) return 0;
        
        $timeLimit = $this->quiz->time_limit;
        $endTime = $this->start_time->addMinutes($timeLimit);
        $remaining = now()->diffInSeconds($endTime, false);
        
        return max(0, $remaining);
    }
}
