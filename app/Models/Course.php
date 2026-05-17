<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'cover_image',
        'code',
        'description',
    ];

    public function quizzes()
    {
        return $this->morphMany(Quiz::class, 'quizable');
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Module::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'course_users')
            ->withTimestamps()
            ->withPivot('enrolled_at', 'completed_at');
    }
}
