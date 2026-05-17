<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'content',
        'order',
        'is_published',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title) . '-' . Str::random(6);
            }
        });

        static::updating(function ($lesson) {
            if ($lesson->isDirty('title') && !$lesson->isDirty('slug')) {
                $lesson->slug = Str::slug($lesson->title) . '-' . $lesson->id;
            }
        });
    }

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

    public function threads()
    {
        return $this->hasMany(DiscussionThread::class);
    }

    public function progress()
    {
        return $this->hasMany(LessonUserProgress::class);
    }
}
