<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'content',
        'order',
        'is_published',
    ];

    protected $casts = [
        'content' => 'array',
        'is_published' => 'boolean',
    ];

    public function quizzes()
    {
        return $this->morphMany(Quiz::class, 'quizable');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
