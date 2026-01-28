<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'city_id',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes')
            ->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function postWeatherSnapshot()
    {
        return $this->hasOne(PostWeatherSnapshot::class);
    }

    public function postImages()
    {
        return $this->hasMany(PostImage::class);
    }
}
