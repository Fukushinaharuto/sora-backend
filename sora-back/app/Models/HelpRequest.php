<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpRequest extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'status',
        'message',
        'address',
        'latitude',
        'longitude',
    ];

    public function helpAssignments()
    {
        return $this->hasMany(HelpAssignment::class, 'help_request_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}
