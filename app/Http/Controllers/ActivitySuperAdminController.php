<?php

namespace App\Http\Controllers;
use App\Models\Activity;
use App\Models\ActivityTrip;
use Illuminate\Http\Request;

class ActivitySuperAdminController extends Controller
{


    public function AddActivity(Request $request)

    {


        $Activity = $request->validate([


        'name' => 'required|string',

        ]);



          Activity::query()->create([

            'name_activity'=> $Activity['name'],

        ]);


        return response()->json(['message' => 'The activity has been added successfully'], 200);



  }


  //---------------------------------------------------------



// get all activities




public function getAllActivities()
{
    $activities = Activity::query()->get();




    if(!$activities){

        return "No activities .";
    }




    return response()->json($activities, 200);
}




  //===================================================================



  public function updateActivity(Request $request,$id)

  {


    $validatedData = $request->validate([

            'name_activity' => 'required|string',

        ]);


        $activity = Activity::query()->findOrFail($id);


        if (!$activity) {
            return "Activity with ID $id not found.";
        }





      $activitytrip = ActivityTrip::query()->where('activity_id',$id)->firstOrFail();





        // البحث عن نشاط موجود
        $activityName = Activity::query()->where('name_activity', $validatedData['name_activity'])->first();


        if($activityName) {


            $activitytrip->update([

                'activity_id'=>$activityName['id'],

                 'name'=>$validatedData['name_activity'],

            ]);

        }

     else {



        $activity->update([

            'name_activity'=>$validatedData['name_activity']
        ]);


        $activitytrip->update([


            'activity_id'=>$activity['id'],


             'name'=>$validatedData['name_activity'],

        ]);


     }






        return response()->json(['message' => 'The activity has been update successfully'],200);



    }





//-------------------------------------------------------------------------


// delete an activity


public function delete_Activity($id) {


    $activity =Activity::find($id);

    if (!$activity) {
        return response()->json(['message' => 'Activity not found'], 404);
    }


    $activity->delete();

    return response()->json(['message' => 'Activity deleted successfully'], 200);


}










//*******************************************************************************************************






}
