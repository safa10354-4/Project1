<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;
    protected $fillable = ['user_trip_id', 'name', 'description','act_duration'];

    public function trip()
    {
        return $this->belongsTo(UserTrip::class);
    }
}
