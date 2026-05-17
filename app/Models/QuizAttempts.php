<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempts extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'is_passed',
        'attempt_number',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAttemptAnswer::class, 'quiz_attempt_id');
    }
}
