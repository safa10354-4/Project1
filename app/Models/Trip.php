<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;



    protected $fillable = [
        'flight_name',
        'location',
        'trip_start_date',
        'trip_end_date',
        'trip_capacity',
        'admin_id'
    ];



    //=================================================================================================


    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }


    //===============================================================================

    public function activities()
    {
        return $this->belongsToMany(Activity::class,'activity_trips');
    }



//=================================================================================================


    public function users()
    {
        return $this->belongsToMany(User::class, 'bookings', 'trip_id', 'user_id');
    }



}
