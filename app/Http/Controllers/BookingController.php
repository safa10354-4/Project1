<?php

namespace App\Http\Controllers;

use App\Models\ActivityTrip;
use App\Models\Booking;
use App\Models\BookingActivityTrip;
use App\Models\Transaction;
use App\Models\Trip;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{



    public function bookTrip(Request $request,$tripId)

    {


        $optionalActivities = $request->validate([

            'optional_activities' => 'array',
            'optional_activities.*.id' => 'exists:activity_trips,id',
        ]);


        $trip = Trip::query()->find($tripId);


        if (!$trip) {

            return response()->json([

                'message' => 'Trip with ID $tripId not found.',

            ],401);

        }


        // التحقق من تاريخ بداية الرحلة
        if ($trip['trip_start_date'] < Carbon::today()) {

            return response()->json([

                'message' => 'It is not possible to book a past flight.',
            ],401);

        }



        // التحقق من توفر المقاعد
        if ($trip['seats_available'] <= 0) {
            return response()->json([

                'message' => 'Sorry, all seats have been reserved for this flight.',

            ],401);

        }


        $totalPrice = $trip['price_non_optional_activities'];


        if (!empty($optionalActivities['optional_activities'])) {

            foreach ($optionalActivities['optional_activities'] as $optionalActivity) {
                $activity = ActivityTrip::query()->find($optionalActivity['id']);
                if ($activity) {
                    $totalPrice += $activity->price;
                }
            }

        }


        $user = auth()->user();

        $wallet = wallet::query()->find($user['id']);
        if ($wallet->balance < $totalPrice) {
            return response()->json([

                'message' => 'Sorry, there is not enough wallet balance to book this trip.',

            ],401);

        }


        $trip->seats_available -= 1;
        $trip->save();


        $booking = Booking::query()->create([

            'user_id' => $user['id'],
            'trip_id' => $trip->id,
            'payment_status' => 'paid',
             'reservation_status' => 'booked_up',//محجوز
            'booking_price' => $totalPrice,

        ]);


//******************************************************************************


        if (!empty($optionalActivities['optional_activities'])) {


            foreach ($optionalActivities['optional_activities'] as $optionalActivity) {

                $activity = ActivityTrip::query()->find($optionalActivity['id']);
                if ($activity) {
                    BookingActivityTrip::query()->create([
                        'booking_id' => $booking['id'],
                        'activity_trip_id' => $optionalActivity['id'],
                    ]);
                }
            }
        }



        //***************************************************************************************

        $wallet['balance'] -= $totalPrice;
        $wallet->save();




        //*************************************************


         $user1=User::query()->findOrFail($user['id']);

                 $user1['balance']= $wallet['balance'];

                 $user1->save();



        //***************************************************
        Transaction::query()->create([
            'wallet_id' => $wallet['id'],
            'amount' => $totalPrice,
            'balance_after_transaction' => $wallet['balance'],
            'type' => 0, // خصم
        ]);



        return response()->json(['message' => 'The flight has been booked successfully',

             'Booking price'=>$booking['booking_price'],


            ],200);




    }


//=============================================================================================================



  //Cancel your trip reservation



    public function cancelBooking($bookingId)
    {
        $booking = Booking::query()->findOrFail($bookingId);

        $trip = Trip::query()->findOrFail($booking['trip_id']);


         $currentDate = now();
        $bookingDate=$booking['created_at'];
        $tripStartDate = Carbon::parse($trip['trip_start_date']);


        $damageTime=$currentDate->diffInSeconds($bookingDate);
        $Time_total= $tripStartDate->diffInSeconds($bookingDate);

        $percent=$damageTime/$Time_total;

        $refundAmount = $booking['booking_price'] * $percent;

         $oldPrice= $booking['booking_price'];

        $booking['reservation_status'] = 'cancelled'; //ملغي
        $booking['booking_price'] = $refundAmount;

        $booking['payment_status']='refunded';
        $booking->save();


        $wallet = wallet::query()->where('user_id',$booking['user_id'])->first();
        $wallet->balance += $oldPrice-$refundAmount;
         $wallet->save();



         //==========================

        $user=User::query()->findOrFail($booking['user_id']);

        $user['balance']= $wallet->balance;

         $user->save();



        //============================



          $trip['seats_available']= $trip['seats_available']+1;

            $trip->save();

         //********************************


        Transaction::query()->create([
            'wallet_id' => $wallet->id,
            'amount' => $oldPrice-$refundAmount,
            'balance_after_transaction' => $wallet->balance,
            'type' => 1, // إعادة
        ]);

        return response()->json(['message' => 'The reservation has been successfully cancelled, then 10% of the reservation price will be refunded to your balance'], 200);
    }







//**************************************************************************************************




    //   get all my reservation for the trip


    public function getAllMyBookings()
    {
        $user = auth()->user();

        $currentDate = now();

        $bookings = Booking::query()
            ->where('user_id', $user->id)
            ->where('reservation_status', 'booked_up')
            ->whereHas('trip', function($query) use ($currentDate) {
                $query->where('trip_start_date', '>', $currentDate);
            })
            ->get();

        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'You currently have no reservations'], 404);
        }

        return response()->json($bookings, 200);
    }


//============================================================================================







    // Hebia



    public function storeReview(Request $request, $id)
    {
        $this->validate($request, [
            'rate' => 'required|integer|min:1|max:5',
//            'comment' => 'required|min:10',
        ]);

        $addReview = Booking::query()->where('user_id',Auth::user()->id)
            ->where('trip_id', $id)
            ->first();

        if ($addReview) {
            $addReview->rate = $request['rate'];
//            $addReview->comment = $request['comment'];
            $addReview->save();

            return response(['message' => 'success thanks for adding review!', 'review' => $addReview]);
        }

        return response(['message' => 'Error: Booking not found'], 404);
    }


    public function storeComment(Request $request, $id)
    {
        $this->validate($request, [
            'comment' => 'required|min:10',
        ]);

        $addcomment = Booking::query()->where('user_id',Auth::user()->id)
            ->where('trip_id', $id)
            ->first();

        if ($addcomment) {
            $addcomment->comment = $request['comment'];
            //   $addReview->comment = $request['comment'];
            $addcomment->save();

            return response(['message' => 'success thanks for adding comment!', 'comment' =>$addcomment]);
        }

        return response(['message' => 'Error: Booking not found'], 404);
    }



//*******************************************************************************







}
