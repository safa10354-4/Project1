<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use App\Models\UserTrip;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show($tripId)
    {

        $trip = UserTrip::findOrFail($tripId);

        if(  $trip){
        $trip->load('activities');


        return response([$trip]);}
     else{

         return response(['message'=>'Trip not found'],404);}
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'user_trip_id' => 'required|exists:user_trips,id',
            'activities' => 'required|array',
            'activities.*.name' => 'required|string|max:255',
            'activities.*.act_duration' =>'required',
            'activities.*.description' => 'nullable|string',
        ]);


        $trip = UserTrip::findOrFail($request->input('user_trip_id'));


        foreach ($request->input('activities') as $activityData) {
            $trip->activities()->create($activityData);
        }

        return response(['message'=>'Done add activity for my trip successful'],200);
    }
    /**
     * Display the specified resource.
     */
    public function update(Request $request, $activityId)
    {

       $data= $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'act_duration'=>'nullable'
        ]);


        $activity = UserActivity::findOrFail($activityId);


        $activity->update($data
        );


        return response(['message'=>'Done updated successful', 'activities'=>$activity],200);
    }


    public function destroy($activityId)
    {

        $activity = UserActivity::findOrFail($activityId);


        $activity->delete();


        return response(['message'=>'Done deleted successful'],200);
    }
}
