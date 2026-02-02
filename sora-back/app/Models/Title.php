<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'image_url',
        'condition_type',
        'condition_value',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_titles', 'title_id', 'user_id')
            ->withTimestamps();
    }
}
