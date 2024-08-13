<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityTrip;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use function Laravel\Prompts\select;

class TripUserController extends Controller
{


    public function getValidTrips()
    {
        // استعلام لاختيار الرحلات التي يكون تاريخ بدايتها في المستقبل أو اليوم الحالي

        $trips = Trip::query()->where(function ($query) {
            $query->where('trip_start_date', '>=', Carbon::today())
                ->where('seats_available', '>', 0);
        })->get();


        // التحقق مما إذا كانت هناك رحلات متاحة أم لا
        if ($trips->isEmpty()) {
            return response()->json(['message' => 'There are not trips'], 404);
        }


        // إرجاع البيانات بتنسيق JSON
        return response()->json($trips, 200);


    }


//==========================================================================================



    public function getAllActivitiesOptional($id)
    {
        $activities = ActivityTrip::query()
            ->where('trip_id', $id)
            ->where('option',0)
            ->select(['id','name','location','price'])
            ->get();


        if(!$activities){

            return "No optional activities in this trip.";
        }



        return response()->json($activities, 200);
    }






//===================================================================================================




    public function getAllActivities_Non_Optional($id)
    {
        $activities = ActivityTrip::query()
              ->where('trip_id', $id)
                ->where('option',1)
               ->select(['id','name','location','price'])
                ->get();




        if(!$activities){

            return "No non optional activities in this trip.";
        }




        return response()->json($activities, 200);
    }







    //====================================================================================================



    // fetch details of a specific activity



    function getActivityDetails($activityId) {


        $activity = ActivityTrip::find($activityId);


        unset($activity['trip_id'],$activity['activity_id'],$activity['option'],$activity['id'],$activity['name']);


        if (!$activity) {
            return 'Activity not found ';


        }



        return response()->json($activity, 200);


    }



    //******************************************************************************************************




    public function search(Request $request){
        // Get the search value from the request
        $search = $request->input('search');

        // Search in the title and body columns from the posts table
        $trips = Trip::query()
            ->where('flight_name', 'LIKE', "%{$search}%")
            ->orWhere('location', 'LIKE', "%{$search}%")->orWhere('price_non_optional_activities','LIKE', "%{$search}%")
            ->get();

        // Return the search view with the resluts compacted


        return response(['message'=> $trips ]);
    }








}
