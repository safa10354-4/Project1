<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;




    protected $fillable = [

         'user_id',

         'trip_id',

         'payment_status',

         'reservation_status',

            'rate',

           'comment',

        'booking_price',

    ];



//=======================================================================================================


    public function activityTrips()
    {
        return $this->belongsToMany(ActivityTrip::class, 'booking_activity_trips', 'booking_id', 'activity_trip_id');
    }

///44444444444444444444444444444444444444444444444444444

    /// new in 7/11/2024 يوم السبت



    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

//444444444444444444444444444444444444444444444444444444
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
