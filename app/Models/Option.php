<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'pair_text',
        'order_sequence',
    ];

    // Hide is_correct from JSON to prevent cheating (will be revealed in review page)
    protected $hidden = [
        'is_correct',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Get parent question
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
