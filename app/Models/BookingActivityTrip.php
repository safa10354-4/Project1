<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingActivityTrip extends Model
{
    use HasFactory;



    protected $fillable = [
        'booking_id',
        'activity_trip_id',
    ];



    //=============================================================================

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function activityTripA()
    {
        return $this->belongsTo(ActivityTrip::class);
    }


    //==========================================================================================



}
