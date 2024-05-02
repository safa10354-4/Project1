<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityTrip;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TripUserController extends Controller
{





    public function getValidTripsWithAvailableSeats()
    {
        $trips = Trip::query()
            ->where('trip_start_date', '>=', Carbon::today())// تأكد من أن تاريخ بداية الرحلة قبل أو يومياً
            ->withCount('users') // حساب عدد المستخدمين المحجوزين لكل رحلة
            ->get()
            ->filter(function ($trip) {
                return $trip->users_count < $trip->trip_capacity; // تصفية الرحلات التي لا تزال لديها مقاعد متاحة
            });
        return response()->json($trips, 200);
    }



    //=================================================================================



    public function getAllActivitiesOptional($id)
    {

        $OptionalActivities = ActivityTrip::query()->where('trip_id', $id )->where('option',1)
            ->get();


        return response()->json($OptionalActivities, 200);

    }



//==========================================================================================

















}
