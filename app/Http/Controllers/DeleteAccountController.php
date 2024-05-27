<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Trip;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DeleteAccountController extends Controller
{
    public function softDelete()
    {
        $userId = auth()->user()->id;
        $user = User::find($userId);

        if ($user) {
            $trips = $user->trips->where('reservation_status', 'booked_up')->where('trip_end_date','>', Carbon::today());


            if (!$trips->isEmpty()) {
                return response()->json(['message' => 'You cannot delete your account because you have reservations that you canceled, then try again']);
            }

            if ($user->balance != 0) {
                return response()->json(['message' => "You have a balance in your wallet. Contact the admin, withdraw it, then try again"]);
            } else {
                $user->delete();
                return response()->json(['message' => "Account deleted"]);
            }

        }

    }



    //====================================================================================================


    }
