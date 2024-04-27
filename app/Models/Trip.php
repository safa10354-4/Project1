<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;





    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }


    public function activities()
    {
        return $this->belongsToMany(Activity::class);
    }






    public function users()
    {
        return $this->belongsToMany(User::class, 'bookings', 'trip_id', 'user_id');
    }



}
