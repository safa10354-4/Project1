<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityTrip;
use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TripAdminController extends Controller
{


    public function addTripWithActivities(Request $request)
    {
        // تحقق من صحة البيانات المرسلة
        $validatedData = $request->validate([
            'flight_name' => 'required|string',
            'location' => 'required|string',
            'trip_start_date' => 'required|date',
            'trip_end_date' => 'required|date',
            'trip_capacity' => 'required|integer',
            'activities' => 'required|array|min:1', // يجب أن يكون هناك على الأقل نشاط واحد
            'activities.*.name' => 'required|string',
            'activities.*.price' => 'required|numeric',
            'activities.*.photo' => 'nullable',
            'activities.*.activity_start_time' => 'required|date',
            'activities.*.activity_end_time' => 'required|date',
            'activities.*.location' => 'required|string',
            'activities.*.option' => 'required|boolean',
            'activities.*.description' => 'required|string',

        ]);

        // إنشاء الرحلة
        $flight = Trip::query()->create([
            'admin_id' => Auth()->user()->id,
            'flight_name' => $validatedData['flight_name'],
            'location' => $validatedData['location'],
            'trip_start_date' => $validatedData['trip_start_date'],
            'trip_end_date' => $validatedData['trip_end_date'],
            'trip_capacity' => $validatedData['trip_capacity'],

            'seats_available'=>$validatedData['trip_capacity']
        ]);



        foreach ($validatedData['activities'] as $activityData) {
            // ابحث عن النشاط بالاسم أو أنشئه إذا لم يكن موجودًا
            $activity = Activity::query()->firstOrCreate(['name_activity' => $activityData['name']]);

            // إنشاء الواصفات للنشاط
            $activityTrip = ActivityTrip::query()->create([
                'trip_id' => $flight['id'],
                'activity_id' => $activity['id'],
                'price' => $activityData['price'],
                //'photo' => $activityData['photo'],
                'activity_start_time' => $activityData['activity_start_time'],
                'activity_end_time' => $activityData['activity_end_time'],
                'location' => $activityData['location'],
                'option' => $activityData['option'],
                'description' => $activityData['description'],

                  'name'=>$activityData['name'],

            ]);

         //***************************************************************
            //   حساب السعر الكلي لرحلة وهو عبالرة عن مجموع اسعار الانشطة الاجبارية
             if($activityData['option']==1){

                 $flight['price_non_optional_activities']= $flight['price_non_optional_activities']+$activityData['price'];

                 $flight->save();
             }

             //******************************************************************

            // التحقق من وجود الصورة وتحميلها
            if (isset($activityData['photo'])) {
                $image = $activityData['photo']; // الوصول إلى الصورة المرفقة مباشرة
                $imageName = $image->hashName();

                Storage::disk("public")->put($imageName, file_get_contents($image));

                // تحديث الوصفة للنشاط برابط الصورة
                $activityTrip->update(['photo' => $imageName]);



            }
        }

        return response()->json(['message' => 'The flight has been added successfully'], 200);


    }



    //==========================================================================================




    public function getAllTrips(){

        $user = auth()->user();
        $trips = $user->trips;

         if(!$trips){

            return "Not found trips";
         }

        // إرجاع البيانات كمصفوفة JSON
        return response()->json($trips, 200);

    }






    //=====================================================================================================




    public function getTripDetails($id){


        $trip=Trip::query()->find($id);

        if (!$trip) {
            return "Trip with ID $id not found.";
        }


        return response()->json($trip, 200);

    }



   //*************************************************************************

    public function getActivityForTrip($id){



        $activities=ActivityTrip::query()->where('trip_id',$id)->get();


         if($activities->isEmpty()){

            return "Not found activities.";
         }

        // if (!$activities) {
        //     return "Trip with ID $id not found.";
        // }

        return response()->json($activities, 200);

    }

//***************************************************************************************************


 // update a details of trip



        public function updateTrip(Request $request,$tripId)
        {
            // تحقق من صحة البيانات المرسلة
            $validatedData = $request->validate([

                'flight_name' => 'nullable|string',
                'location' => 'nullable|string',
                'trip_start_date' => 'nullable|date',
                'trip_end_date' => 'nullable|date',
                'trip_capacity' => 'nullable|integer',
            ]);


            $trip = Trip::query()->findOrFail($tripId);


            if (!$trip) {
                return "Trip with ID $tripId not found.";
            }


            if (!empty($validatedData)) {
            $trip->update($validatedData);
        }



            return response()->json(['message' => 'The flight has been update successfully'],200);



        }



        //**********************************************************************************



     public function updateActivity(Request $request,$Id)
     {

         $validatedData = $request->validate([


             'name' => 'nullable|string',
             'price' => 'nullable|numeric',
             'photo' => 'nullable',
             'activity_start_time' => 'nullable|date',
             'activity_end_time' => 'nullable|date',
             'location' => 'nullable|string',
             'option' => 'nullable|boolean',
             'description' => 'nullable|string',
         ]);


         $activityTrip = ActivityTrip::query()->find($Id);


         $trip =Trip::query()->where('id',$activityTrip['trip_id'])->first();


         if (!$activityTrip) {
             return "Activity with ID $Id not found.";
         }


         //===================================================================


         if (isset($validatedData['name'])) {
             // البحث عن نشاط موجود
             $activityName = Activity::query()->where('name_activity', $validatedData['name'])->first();


             if($activityName) {
                 $activityTrip['activity_id'] = $activityName->id;

             }


          else {
              // إذا لم يتم العثور على نشاط موجود، قم بإنشاء نشاط جديد
              $activityName = Activity::query()->create([

                  'name_activity' => $validatedData['name']

              ]);


              //  تحديث activity_id في ActivityTrip للإشارة إلى النشاط الجديد

              $activityTrip['activity_id'] = $activityName['id'];



          }

             $activityTrip->save();

         }


         //=============================================================================================


         if ($activityTrip) {



             if (isset($validatedData['option']) && isset($validatedData['price']) ) {



                     if ($activityTrip['option'] == 0 && $validatedData['option'] == 1) {


                     $trip['price_non_optional_activities'] = $trip['price_non_optional_activities'] + $validatedData['price'];


                 } else if ($activityTrip['option'] == 1 && $validatedData['option'] == 0) {


                     $trip['price_non_optional_activities'] = $trip['price_non_optional_activities'] -  $activityTrip['price'];

                 }

             }


             else if (isset($validatedData['option'])){


                if ($activityTrip['option'] == 0 && $validatedData['option'] == 1) {


                    $trip['price_non_optional_activities'] = $trip['price_non_optional_activities'] + $activityTrip['price'];


                } else if ($activityTrip['option'] == 1 && $validatedData['option'] == 0) {


                    $trip['price_non_optional_activities'] = $trip['price_non_optional_activities'] - $activityTrip['price'];

                }
             }



             else if (isset($validatedData['price'])){


                if ($activityTrip['option'] == 1)

                $trip['price_non_optional_activities'] = $trip['price_non_optional_activities'] - $activityTrip['price'] + $validatedData['price'];



             }

             $trip->save();




             if (!empty($validatedData)) {
                $activityTrip->update($validatedData);
            }



         }




         return response()->json(['message' => 'Modified  successfully'], 200);


     }










    //************************************************************************************************

    public function deleteTrip($id) {


        $trip = Trip::find($id);

        if (!$trip) {
            return response()->json(['message' => 'Flight not found'], 404);
        }

        $currentDate = now();
        $tripEndDate = $trip->trip_end_date;

        if ($currentDate < $tripEndDate || $trip['seats_available']!=$trip['trip_capacity'] ) {

            return response()->json(['message' => 'Cannot delete a valid trip'], 403);
        }

        $trip->delete();

        return response()->json(['message' => 'Trip deleted successfully'], 200);


    }


    //*****************************************************************************************









//*******************************************************************************************************



   //Hebia




    public function getAverageRating($tripId)
    {

        $averageRating = Booking::where('trip_id', $tripId)->avg('rate');


        return response()->json(['averageRating' => $averageRating], 404);

    }





//*************************************************************************************************



























}
