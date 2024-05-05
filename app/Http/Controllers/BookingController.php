<?php

namespace App\Http\Controllers;

use App\Models\ActivityTrip;
use App\Models\Booking;
use App\Models\BookingActivityTrip;
use App\Models\Transaction;
use App\Models\Trip;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{





    public function bookTrip(Request $request,$tripId)

    {


        $optionalActivities = $request->validate([
            // 'trip_id' => 'required|exists:trips,id',
            'optional_activities' => 'array',
            'optional_activities.*.id' => 'exists:activity_trips,id',
        ]);


        // العثور على الرحلة المطلوبة
        $trip = Trip::find($tripId);


        if (!$trip) {
            return "Trip with ID $tripId not found.";
        }


        // التحقق من تاريخ بداية الرحلة
        if ($trip['trip_start_date'] < Carbon::today()) {
            return "لا يمكن حجز رحلة ماضية.";
        }


        // التحقق من توفر المقاعد
        if ($trip['seats_available'] <= 0) {
            return "عذرًا، جميع المقاعد تم حجزها لهذه الرحلة.";
        }

        // حساب سعر الرحلة
        $totalPrice = $trip['price_non_optional_activities'];

        // إضافة تكلفة الأنشطة الاختيارية إذا كانت محددة
        if (!empty($optionalActivities['optional_activities'])) {

            foreach ($optionalActivities['optional_activities'] as $optionalActivity) {
                $activity = ActivityTrip::find($optionalActivity['id']);
                if ($activity) {
                    $totalPrice += $activity->price;
                }
            }

        }

        // التحقق من رصيد المحفظة
        $user = auth()->user();


        $existingBooking = Booking::where('trip_id', $tripId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingBooking) {
            return "عذرًا، لا يمكنك حجز نفس الرحلة مرة أخرى.";
        }


        $wallet = wallet::query()->find($user->id);


        if ($wallet->balance < $totalPrice) {
            return "عذرًا، رصيد المحفظة غير كافٍ لحجز هذه الرحلة.";
        }

        // تخصيص مقعد وتحديث عدد المقاعد المتاحة
        $trip->seats_available -= 1;
        $trip->save();

        // إنشاء سجل في جدول الحجوزات
        $booking = Booking::create([
            'user_id' => $user->id,
            'trip_id' => $trip->id,
            'payment_status' => 'paid',
             'reservation_status' => 'confirmed',
            //'rate' => $totalPrice,
            //'comment' => '',
        ]);


//******************************************************************************


        if (!empty($optionalActivities['optional_activities'])) {


            foreach ($optionalActivities['optional_activities'] as $optionalActivity) {

                $activity = ActivityTrip::find($optionalActivity['id']);
                if ($activity) {
                    BookingActivityTrip::create([
                        'booking_id' => $booking->id,
                        'activity_trip_id' => $optionalActivity['id'],
                    ]);
                }
            }
        }


        //$booking->activityTrips()->attach($activityId->id);




        //***************************************************************************************

        // خصم سعر الرحلة من رصيد المحفظة وإضافة سجل في جدول العمليات
        $wallet->balance -= $totalPrice;
        $wallet->save();

        Transaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $totalPrice,
            'balance_after_transaction' => $wallet->balance,
            'type' => 0, // خصم
        ]);

        //return "تم حجز الرحلة بنجاح.";


        return response()->json(['message' => 'The flight has been booked successfully'], 200);


    }


//**********************************************************************************************










}
