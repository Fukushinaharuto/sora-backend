<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostWeatherSnapshot extends Model
{
    protected $fillable = [
        'post_id',
        'weather_type',
        'temperature',
        'feels_like',
        'wind_speed',
        'wind_direction',
        'precipitation_prob',
        'visibility',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
