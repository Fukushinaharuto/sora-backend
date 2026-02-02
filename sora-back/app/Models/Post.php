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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes', 'post_id', 'user_id')
            ->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function postWeatherSnapshot()
    {
        return $this->hasOne(PostWeatherSnapshot::class, 'post_id', 'id');
    }

    public function postImages()
    {
        return $this->hasMany(PostImage::class, 'post_id', 'id');
    }
}
