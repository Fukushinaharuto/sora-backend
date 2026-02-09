<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    protected $fillable = ['name'];

    public function cities()
    {
        return $this->hasMany(City::class, 'prefecture_id', 'id');
    }
}
