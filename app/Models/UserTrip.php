<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTrip extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description','flight_duration','flight_date'];

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }
}
