<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'quizable_id',
        'quizable_type',
        'title',
        'type',
        'passing_score',
        'time_limit',
    ];

    public function quizable()
    {
        return $this->morphTo();
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }
}
