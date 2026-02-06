<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'prefecture_id',
        'name',
        'latitude',
        'longitude',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'city_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'city_id', 'id');
    }

    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class, 'prefecture_id', 'id');
    }

    public function helpRequests()
    {
        return $this->hasMany(HelpRequest::class, 'city_id', 'id');
    }
}
