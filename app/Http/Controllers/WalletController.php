<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\wallet;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{


    public function Wallet_charging(Request $request, $id)
    {
        $wallet = Wallet::query()->where('user_id', $id)->first();

        $wallet['balance'] = $wallet['balance'] + $request['new_balance'];


        $wallet->save();

        Transaction::query()->create([

            'wallet_id' => $wallet['user_id'],
            'amount' => $request['new_balance'],
            'balance_after_transaction' => $wallet['balance'],
            'type' => 1,
        ]);




        return response([

            'message'=>'The wallet was charged successfully.',

           'The quantity charged'=> $request['new_balance']

            ]);

    }


    //===========================================================================









}





