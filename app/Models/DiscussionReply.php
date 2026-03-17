<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionReply extends Model
{
    protected $fillable = [
        'thread_id',
        'user_id',
        'content',
        'parent_id' // optional
    ];

    public function thread()
    {
        return $this->belongsTo(DiscussionThread::class, 'thread_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Nested reply
    public function parent()
    {
        return $this->belongsTo(DiscussionReply::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(DiscussionReply::class, 'parent_id');
    }
}