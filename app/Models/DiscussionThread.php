<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionThread extends Model
{
    protected $fillable = [
        'lesson_id',
        'user_id',
        'title',
        'content'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(DiscussionReply::class, 'thread_id');
    }
}