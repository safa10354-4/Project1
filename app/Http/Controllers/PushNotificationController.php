<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;



class PushNotificationController extends Controller
{
//    public function sendPushNotification(Request $request)
//    {
//
//        $request->validate([
//            'title' => 'required|string|max:255',
//            'body' => 'required|string|max:255',
//            'token' => 'required|string',
//        ]);
//
//        $title = $request->input('title');
//        $body = $request->input('body');
//        $deviceToken = $request->input('token');
//
//        try {
//            $factory = (new Factory)
//                ->withServiceAccount(base_path('config/firebase_credentials.json'));
//
//            $messaging = $factory->createMessaging();
//
//            $notification = Notification::create($title, $body);
//
//            $message = CloudMessage::withTarget('token', $deviceToken)
//                ->withNotification($notification);
//
//            $messaging->send($message);
//
//            return response()->json(['success' => true, 'message' => 'Notification sent to device token successfully.']);
//        } catch (\Exception $e) {
//            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
//        }
//    }




  //************************************************************************************************



    public function sendPushNotification($title, $body, $token)
    {

        $deviceToken =$token;

        try {
            $factory = (new Factory)
                ->withServiceAccount(base_path('config/firebase_credentials.json'));

            $messaging = $factory->createMessaging();

            $notification = Notification::create($title, $body);

            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification);

            $messaging->send($message);

            return response()->json(['success' => true, 'message' => 'Notification sent to device token successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }




    //****************************************************************************


    public function create_device_token(Request $request)
    {

        $request->validate([
            'device_token' => 'required|string',
        ]);


        $user = auth()->user();

        if ($user) {

            $user['device_token'] = $request['device_token'];

            $user->save();

            return response()->json(['success' => true, 'message' => 'Device token updated successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }
    }





}

