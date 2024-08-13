<?php
//
//namespace App\Http\Controllers;
//
//use App\Models\Transaction;
//use App\Models\wallet;
//use Illuminate\Foundation\Auth\User;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
//
//class WalletController extends Controller
//{
//
//
//    public function Wallet_charging(Request $request, $id)
//    {
//        $wallet = Wallet::query()->where('user_id', $id)->first();
//
//         $user=User::query()->findOrFail($id);
//
//        $wallet['balance'] = $wallet['balance'] + $request['new_balance'];
//
//
//        $wallet->save();
//
//
//
//         //*********************
//
//           $user['balance']=$wallet['balance'];
//
//           $user->save();
//
//        //*********************************
//        Transaction::query()->create([
//
//            'wallet_id' => $wallet['user_id'],
//            'amount' => $request['new_balance'],
//            'balance_after_transaction' => $wallet['balance'],
//            'type' => 1,
//        ]);
//
//
//
//
//        return response([
//
//            'message'=>'The wallet was charged successfully.',
//
//           'The quantity charged'=> $request['new_balance']
//
//            ]);
//
//    }
//
//
//    //===========================================================================
//
//
//
//
//
//
//
//
//
//}
//
//
//
//
//

//************************************************************************************************************


namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function Wallet_charging(Request $request,$id)
    {

        $request->validate([
            'new_balance'=>'required|numeric',
        ]);

        $wallet = Wallet::query()->where('user_id', $id)->first();
        $user = User::query()->findOrFail($id);

        $wallet['balance'] = $wallet['balance'] + $request['new_balance'];
        $wallet->save();

        //*********************
        $user['balance'] = $wallet['balance'];
        $user->save();
        //*********************************

        Transaction::query()->create([
            'wallet_id' => $wallet['user_id'],
            'amount' => $request['new_balance'],
            'balance_after_transaction' => $wallet['balance'],
            'type' => 1,
        ]);


        //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


        // إرسال إشعار للمستخدم
        $pushNotificationController = new PushNotificationController();
        $title = 'شحن المحفظة';
        $body = 'تم شحن محفظتك برصيد قدره ' . $request['new_balance'];


        $token =$user['device_token'];

        if ($token) {
            $pushNotificationController->sendPushNotification($title, $body, $token);
        }


        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


        return response([
            'message' => 'The wallet was charged successfully.',
            'The quantity charged' => $request['new_balance']
        ]);
    }


}
