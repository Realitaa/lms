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

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }
}
