<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityTrip extends Model
{
    use HasFactory;



    protected $fillable = [

        'trip_id',
        'activity_id',
        'price',
        'photo',
        'activity_start_time',
        'activity_end_time',
        'location',

        'option',

         'description'

    ];


    //===============================================================================




    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_activity_trips', 'activity_trip_id', 'booking_id');
    }




}
