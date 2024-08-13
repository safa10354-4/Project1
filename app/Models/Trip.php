<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory;

   use SoftDeletes;

    protected $fillable = [
        'flight_name',
        'location',
        'trip_start_date',
        'trip_end_date',
        'trip_capacity',
        'admin_id',
         'seats_available',
        'price_non_optional_activities',
        'rates',
        'image',
        'key'
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

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

}
