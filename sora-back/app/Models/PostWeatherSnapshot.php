<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostWeatherSnapshot extends Model
{
    protected $fillable = [
        'post_id',
        'weather_type',
        'temperature',
        'wind_speed',
        'wind_direction',
        'precipitation',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
