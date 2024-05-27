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






}
