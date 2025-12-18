<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Question extends Model
{
    protected $fillable = [
        'quiz_id',
        'content',
        'type',
        'media_url',
        'points',
        'explanation',
        'reference',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Delete media file when question is deleted
        static::deleting(function ($question) {
            if ($question->media_url) {
                Storage::disk('public')->delete($question->media_url);
            }
            
            // Also delete all options
            $question->options()->delete();
        });
    }

    /**
     * Get parent quiz
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get question options
     */
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    /**
     * Get correct options (for single/multiple choice)
     */
    public function correctOptions()
    {
        return $this->options()->where('is_correct', true);
    }

    /**
     * Check if answer is correct based on question type
     */
    public function checkAnswer($userAnswer): bool
    {
        if (empty($userAnswer)) {
            return false;
        }

        switch ($this->type) {
            case 'single_choice':
                $correctId = $this->correctOptions()->first()?->id;
                return (int) $userAnswer == $correctId;
                
            case 'multiple_choice':
                $correctIds = $this->correctOptions()->pluck('id')->sort()->values()->toArray();
                $userIds = collect($userAnswer)->map(fn($v) => (int) $v)->sort()->values()->toArray();
                return $correctIds === $userIds;
                
            case 'ordering':
                // userAnswer format: {option_id: sequence_number}
                // Check if user's sequence matches correct order_sequence for each option
                $options = $this->options()->get();
                foreach ($options as $option) {
                    $optionId = (string) $option->id;
                    if (!isset($userAnswer[$optionId])) {
                        return false;
                    }
                    if ((int) $userAnswer[$optionId] !== $option->order_sequence) {
                        return false;
                    }
                }
                return true;
                
            case 'matching':
                // userAnswer format: {option_id: selected_pair_text}
                // Check if user selected the correct pair_text for each option
                $options = $this->options()->get();
                foreach ($options as $option) {
                    $optionId = (string) $option->id;
                    if (!isset($userAnswer[$optionId])) {
                        return false;
                    }
                    if ($userAnswer[$optionId] !== $option->pair_text) {
                        return false;
                    }
                }
                return true;
                
            default:
                return false;
        }
    }
}
