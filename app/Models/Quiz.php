<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quiz extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'time_limit',
        'is_active',
        'randomize_questions',
        'question_limit',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'randomize_questions' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Auto-generate slug on creation
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($quiz) {
            if (empty($quiz->slug)) {
                $quiz->slug = Str::slug($quiz->title);
            }
        });
    }

    /**
     * Get quiz category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get quiz questions
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get quiz attempts
     */
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get total questions count
     */
    public function getTotalQuestionsAttribute(): int
    {
        return $this->questions()->count();
    }

    /**
     * Get total points available
     */
    public function getTotalPointsAttribute(): int
    {
        return $this->questions()->sum('points');
    }

    /**
     * Check if quiz is available (schedule-wise)
     */
    public function isAvailable(): bool
    {
        $now = now();

        // Check start time
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        // Check end time
        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return $this->is_active;
    }

    /**
     * Get schedule status label
     */
    public function getScheduleStatusAttribute(): string
    {
        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return 'coming_soon';
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return 'closed';
        }

        return $this->is_active ? 'open' : 'inactive';
    }
}
