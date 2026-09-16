<?php

namespace App\Http\Controllers;
use App\Models\Activity;
use App\Models\ActivityTrip;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Trip;
use App\Models\wallet;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TripAdminController extends Controller
{
    public function addTripWithActivities(Request $request)
    {

        $validatedData = $request->validate([
            'flight_name' => 'required|string',
            'location' => 'required|string',
            'trip_start_date' => [
                'required',
                'date',
                'after_or_equal:'.Carbon::now()->format('Y-m-d')
            ],
            'trip_end_date' => [
                'required',
                'date',
                'after_or_equal:trip_start_date'
            ],
            'trip_capacity' => 'required|integer',
            'image' => 'nullable|file|image',
            'activities' => 'required|array|min:1',
            'activities.*.name' => 'required|string',
            'activities.*.price' => 'required|numeric',
            'activities.*.photo' => 'nullable|file|image',
            'activities.*.activity_start_time' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $tripStartDate = Carbon::parse($request->trip_start_date);
                    $tripEndDate = Carbon::parse($request->trip_end_date);

                    if (Carbon::parse($value)->lt($tripStartDate) || Carbon::parse($value)->gt($tripEndDate)) {
                        $fail("The {$attribute} must be between the trip start date and the trip end date.");
                    }
                }
            ],
            'activities.*.activity_end_time' => [
                'required',
                'date',
                'after_or_equal:activities.*.activity_start_time',
                function ($attribute, $value, $fail) use ($request) {
                    $tripStartDate = Carbon::parse($request->trip_start_date);
                    $tripEndDate = Carbon::parse($request->trip_end_date);

                    if (Carbon::parse($value)->lt($tripStartDate) || Carbon::parse($value)->gt($tripEndDate)) {
                        $fail("The {$attribute} must be between the trip start date and the trip end date.");
                    }
                }
            ],
            'activities.*.location' => 'required|string',
            'activities.*.option' => 'required|boolean',
            'activities.*.description' => 'required|string',
            'activities.*.latitude' => 'required',
            'activities.*.longitude' => 'required',
        ]);


        //--------------------------------------------------------------------------------------------

        $flight = Trip::query()->create([
            'admin_id' => Auth()->user()->id,
            'flight_name' => $validatedData['flight_name'],
            'location' => $validatedData['location'],
            'trip_start_date' => $validatedData['trip_start_date'],
            'trip_end_date' => $validatedData['trip_end_date'],
            'trip_capacity' => $validatedData['trip_capacity'],
            'seats_available' => $validatedData['trip_capacity'],
            'image' => null,
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $avatarName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $avatarName);


            $imagePath = 'images/' . $avatarName;

            $flight->update(['image' => $imagePath]);
        }

        foreach ($validatedData['activities'] as $activityData) {

            $activity = Activity::firstOrCreate(['name_activity' => $activityData['name']]);


            $activityTrip = ActivityTrip::create([
                'trip_id' => $flight->id,
                'activity_id' => $activity->id,
                'price' => $activityData['price'],
                'activity_start_time' => $activityData['activity_start_time'],
                'activity_end_time' => $activityData['activity_end_time'],
                'location' => $activityData['location'],
                'option' => $activityData['option'],
                'description' => $activityData['description'],
                'name' => $activityData['name'],
                'latitude' => $activityData['latitude'],
                'longitude' => $activityData['longitude'],
            ]);

            if ($activityData['option'] == 1) {
                $flight->price_non_optional_activities += $activityData['price'];
                $flight->save();
            }

            if (isset($activityData['photo']) && $activityData['photo']->isValid()) {
                $image = $activityData['photo'];
                $avatarName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $avatarName);


                $imagePath = 'images/' . $avatarName;

                $activityTrip->update(['photo' => $imagePath]);
            }
        }



        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


        $Users = User::all();

        foreach ($Users as $User) {
            if ($User) {
                $user = User::query()->find($User['id']);

                if ($user) {
                    $pushNotificationController = new PushNotificationController();
                    $title = ' اضافة رحلة  ';
                    $body = ' تمت اضافة رحلة جديدة الى  ' . $validatedData['location'];

                    $token = $user['device_token'];

                    if ($token) {
                        $pushNotificationController->sendPushNotification($title, $body, $token);
                    }
                }
            }
        }

        //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


        return response()->json(['message' => 'The flight has been added successfully'], 200);
    }


    //=================================================================================================================



    public function getAllTrips() {
        $user = auth()->user();
        $trips = $user->trips;

        if ($trips->isEmpty()) {
            return response()->json(['message' => 'Not found trips'], 404);
        }

        foreach ($trips as $trip) {
            $ratings = Booking::where('trip_id', $trip->id)->pluck('rate')->filter(function ($value) {
                return is_numeric($value) && $value > 0;
            });
            
            if (!$ratings->isEmpty()) {
                $averageRating = $ratings->avg();
                $trip->rates = $averageRating;
            } else {
              
                $trip->rates = 0; 
            }

            
            $trip->save();
        }

        return response()->json($trips, 200);
    }



    //=====================================================================================================

    public function getTripDetails($id)
    {



//        $trip = Trip::query()->where('admin_id',$user['id'])->where('id',$id)->get();


        $user = auth()->user();


        $trip = Trip::query()->where('admin_id', $user['id'])
            ->where('id', $id)
            ->first();


        if (!$trip) {
            return response()->json(['message' => "You have no trips with ID $id."], 404);
        }


        $tripId = intval($id);

        $ratings = Booking::where('trip_id', $tripId)->pluck('rate')->filter(function ($value) {
            return is_numeric($value) && $value > 0;
        });
        
        if ($ratings->isEmpty()) {
            return response()->json(['message' => $trip], 200);
        }
        $averageRating = $ratings->avg();
        $trip->rates = $averageRating;
        $trip->save();

        return response()->json(['message' =>$trip],200);
    }

    //*************************************************************************


  //  public function getActivityForTrip($id){



//        $activities=ActivityTrip::query()->where('trip_id',$id)->get();
//
//
//        if($activities->isEmpty()){
//
//            return "Not found activities.";
//        }
//
//        // if (!$activities) {
//        //     return "Trip with ID $id not found.";
//        // }
//
//        return response()->json($activities, 200);
//
//    }




        public function getActivityForTrip($id)
        {
            $user = auth()->user();


            $trip = Trip::query()->where('id', $id)
                ->where('admin_id', $user['id'])
                ->first();


            if (!$trip) {
                return response()->json(['message' => "You have no trips with ID $id."], 404);
            }


            $activities = ActivityTrip::query()->where('trip_id', $id)->get();


            if ($activities->isEmpty()) {
                return response()->json(['message' => "No activities found for this trip."], 404);
            }


            return response()->json($activities, 200);
        }





//***************************************************************************************************


    // update a details of trip

    public function updateTrip(Request $request,$tripId)
    {

        $validatedData = $request->validate([

            'flight_name' => 'nullable|string',
            'location' => 'nullable|string',
            'trip_start_date' => 'nullable|date',
            'trip_end_date' => 'nullable|date',
            'trip_capacity' => 'nullable|integer',
            'image'=>'nullable',
        ]);


        $trip = Trip::query()->findOrFail($tripId);


        if ($trip['admin_id'] !== auth()->id()) {
            return response()->json(['message' => "You have no trips with ID $tripId."], 403);

        }


        if (!empty($validatedData)) {
            $trip->update($validatedData);
        }



        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


        $Users = User::all();

        foreach ($Users as $User) {
            if ($User) {
                $user = User::query()->find($User['id']);

                if ($user) {
                    $pushNotificationController = new PushNotificationController();
                    $title = ' تعديل رحلة   '.$trip['flight_name'];
                    $body = ' تم تعديل الرحلة التي ستقام في  ' . $trip['location'];

                    $token = $user['device_token'];

                    if ($token) {
                        $pushNotificationController->sendPushNotification($title, $body, $token);
                        return response()->json(['message' => 'الاشعار قد وصل '],200);


                    }
                }
            }
        }

//----------------------------------------------------------------------------------------------------

        return response()->json(['message' => 'The flight has been update successfully'],200);



    }


    //***********************************************************************************************


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
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);


        $activityTrip = ActivityTrip::query()->find($Id);


        $trip =Trip::query()->where('id',$activityTrip['trip_id'])->first();




        if ($trip['admin_id'] !== auth()->id()) {
            return response()->json(['message' => "You have no activity with ID $Id."], 403);

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

    //Hebia


    public function getComment($tripId)
    {

        $comments = Booking::where('trip_id', $tripId)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')->get();
        // if (  $comments->isEmpty()) {
        //     return response()->json(['message' => "not found comments"], 200);
        // }

        $formattedComments = $comments->map(function ($booking) {
            return [
                'comment' => $booking->comment,
                'user_name' => $booking->user->name,

            'created_at' => $booking->created_at
            ];
        });

        return response()->json(['comments' => $formattedComments], 200);
    }




    //*************************************************************************************************
    //******************************************************************************************************************

    public function deleteTrip($id) {


        $trip = Trip::query()->find($id);


        if ($trip['admin_id'] !== auth()->id()) {
            return response()->json(['message' => "You have no trips with ID $id."], 403);

        }



        $currentDate = now();
        $tripEndDate = $trip['trip_end_date'];


        if($currentDate >= $tripEndDate){

            // soft delete to this trip

            $trip->delete();

        }


        else {

            $UsersBooking = Booking::query()->where('trip_id', $id)->get();


            foreach ($UsersBooking as $UserBooking) {

                $user = User::query()->find($UserBooking['user_id']);

                $wallet = Wallet::query()->where('user_id', $user['id'])->first();
                $wallet['balance'] += $UserBooking['booking_price'];
                $wallet->save();

                //*********************
                $user['balance'] = $wallet['balance'];
                $user->save();
                //************************

                Transaction::query()->create([
                    'wallet_id' => $wallet['user_id'],
                    'amount' => $UserBooking['booking_price'],
                    'balance_after_transaction' => $wallet['balance'],
                    'type' => 1,
                ]);

                $UserBooking->delete();


                //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

                // إرسال إشعار للمستخدم
                $pushNotificationController = new PushNotificationController();
                $title = 'الغاء حجز الرحلة ';
                $body = 'تم الغاء حجزك في الرحلة وارجاع سعر الحجز الى محفظتك  ' . $trip['flight_name'];


                $token = $user['device_token'];

                if ($token) {
                    $pushNotificationController->sendPushNotification($title, $body, $token);
                }


                //+++++++++++++++++++++++++++++++++++++++++++++++++++

            }

            $trip->delete();

        }


        return response()->json(['message' => 'Trip deleted successfully'], 200);



    }


//*******************************************************************************************************





}
