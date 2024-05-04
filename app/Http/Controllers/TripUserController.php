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
        // استعلام لاختيار الرحلات التي يكون تاريخ بدايتها في المستقبل أو اليوم الحالي
        $trips = Trip::query()
            ->where('trip_start_date', '>=', Carbon::today())

            // استرجاع عدد المستخدمين المحجوزين لكل رحلة
            ->withCount('users')

            // تحديد الحقول المطلوبة فقط من جدول الرحلات
       //->select('flight_name', 'location', 'trip_start_date', 'trip_end_date')
            // استرجاع البيانات
            ->get()

            // تصفية الرحلات التي لا تزال لديها مقاعد متاحة
            ->filter(function ($trip) {
                return $trip->users_count < $trip->trip_capacity;
            });

        // إرجاع البيانات بتنسيق JSON

        return response()->json($trips,200);

    }

    //=================================================================================



//    public function getAllActivitiesOptional($id)
//    {
//
//        $OptionalActivities = ActivityTrip::where('trip_id', $id )->where('option','and',1)
//            ->get();
//
//
//        return response()->json($OptionalActivities, 200);
//
//
//    }



    public function getValidTripsWithAvailableSeatsAndActivities() {
        // استعلام لاختيار الرحلات التي يكون تاريخ بدايتها في المستقبل أو اليوم الحالي
        $trips = Trip::query()
            ->where('trip_start_date', '>=', Carbon::today())
            // استرجاع عدد المستخدمين المحجوزين لكل رحلة
            ->withCount('users')
            // تحديد الحقول المطلوبة فقط من جدول الرحلات
            // استرجاع البيانات
            ->get()
            // تصفية الرحلات التي لا تزال لديها مقاعد متاحة وتنتمي لها نشاطات
            ->filter(function ($trip) {
                return $trip->users_count < $trip->trip_capacity &&
                    $this->hasActivities($trip->id);
            })
            // تضمين النشاطات التي تحقق الشروط
            ->map(function ($trip) {
                $trip->activities = ActivityTrip::where('trip_id', $trip->id)
                    ->where('option', 1)
                    ->get();
                return $trip;
            });

        // إرجاع البيانات بتنسيق JSON
        return response()->json($trips, 200);
    }
//==========================================================================================






















}
