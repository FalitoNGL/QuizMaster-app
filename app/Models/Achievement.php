<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'criteria_type',
        'criteria_value',
        'xp_reward',
    ];

    /**
     * Users who have earned this achievement
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot('earned_at')
            ->withTimestamps();
    }
}
