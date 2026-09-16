<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityTrip;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use function Laravel\Prompts\select;
use function PHPUnit\Framework\isEmpty;

class TripUserController extends Controller
{

    public function getValidTrips()
    {

        $userId = auth()->id();
        $trips = Trip::query()->where(function ($query) {
            $query->where('trip_start_date', '>=', Carbon::today())
                ->where('seats_available', '>', 0);
        })->get();

        foreach ($trips as $trip) {
            // Check if the hotel is a favorite for the authenticated user
            $trip->is_favorite = $trip->favorites->contains('user_id', $userId);
            // Remove the favorites relationship from the response
            unset($trip->favorites);
        }

        if ($trips->isEmpty()) {
            return response()->json(['message' => 'There are not trips'], 404);
        }

        return response()->json($trips, 200);


    }





//==========================================================================================



    public function getAllActivitiesOptional($id)
    {

        $trip = Trip::query()->find($id);
        if ( !$trip ) {
            return response()->json(['message' => "You have no trips with ID $id."], 403);

        }

        else {
            $activities = ActivityTrip::query()
                ->where('trip_id', $id)
                ->where('option', 0)
                ->select(['id', 'name', 'location', 'price', 'photo', 'activity_start_time', 'activity_end_time', 'description',])
                ->get();


            if ($activities->isEmpty()) {
                return "No optional activities in this trip.";
            }

           else
               return response()->json($activities, 200);

        }
    }






//===================================================================================================




    public function getAllActivities_Non_Optional($id)
    {


        $trip = Trip::query()->find($id);
        if ( !$trip ) {
            return response()->json(['message' => "You have no trips with ID $id."], 403);

        }


        else {

            $activities = ActivityTrip::query()
                ->where('trip_id', $id)
                ->where('option', 1)
                ->select(['id', 'name', 'location', 'price', 'photo', 'activity_start_time', 'activity_end_time', 'description',])
                ->get();


            if ($activities->isEmpty()){

                return "No non optional activities in this trip.";
            }

            else
            return response()->json($activities, 200);

        }
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
        $userId = auth()->id();
        $search = $request->input('search');

        // Search in the title and body columns from the posts table
        $trips = Trip::query()
            ->where('flight_name', 'LIKE', "%{$search}%")
            ->orWhere('location', 'LIKE', "%{$search}%")->orWhere('price_non_optional_activities','LIKE', "%{$search}%")
            ->get();
        foreach ($trips as $trip) {
            // Check if the hotel is a favorite for the authenticated user
            $trip->is_favorite = $trip->favorites->contains('user_id', $userId);
            // Remove the favorites relationship from the response
            unset($trip->favorites);
        }


        return response(['message'=> $trips ]);
    }








}
