<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityTrip;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TripUserController extends Controller
{




    public function getValidTrips() {
        // استعلام لاختيار الرحلات التي يكون تاريخ بدايتها في المستقبل أو اليوم الحالي

        $trips = Trip::where(function ($query) {
            $query->where('trip_start_date', '>=', Carbon::today())
                ->where('seats_available','>',0);
           })->get();




        // التحقق مما إذا كانت هناك رحلات متاحة أم لا
        if ($trips->isEmpty()) {
            return response()->json(['message' => 'There are not trips'], 404);
        }



              // إرجاع البيانات بتنسيق JSON
              return response()->json($trips, 200);



          }



//==========================================================================================



            public function getAllActivitiesOptional($id) {
                $optionalActivities = Trip::find($id)
                    ->where('trip_start_date', '>=', Carbon::today())
                    ->where('seats_available', '>', 0)
                    ->with(['activities' => function ($query) {
                        $query->where('option', 0)->select('name_activity');
                    }])
                    ->get();


                // استخراج الأنشطة الاختيارية من كل رحلة وتجميعها في مصفوفة واحدة
                $activities = [];
                foreach ($optionalActivities as $trip) {
                    $activities = array_merge($activities, $trip->activities->toArray());
                }


                if ( empty( $activities)) {
                    return response()->json(['message' => 'There are no optional activities.'], 404);
                }


                return response()->json($activities, 200);
            }


//===================================================================================================






    public function getAllActivities_Non_Optional($id) {


        $optionalActivities = Trip::find($id)
            ->where('trip_start_date', '>=', Carbon::today())
            ->where('seats_available', '>', 0)
            ->with(['activities' => function ($query) {
                $query->where('option',1)->select('name_activity');
            }])
            ->get();


        // استخراج الأنشطة الاختيارية من كل رحلة وتجميعها في مصفوفة واحدة
        $activities = [];
        foreach ($optionalActivities as $trip) {
            $activities = array_merge($activities, $trip->activities->toArray());
        }


        if ( empty( $activities)) {
            return response()->json(['message' => 'There are not  non_optional activities.'], 404);
        }


        return response()->json($activities, 200);
    }


    //====================================================================================================



















}
