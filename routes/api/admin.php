<?php

use App\Http\Controllers\ActivitySuperAdminController;
use App\Http\Controllers\ControlpanelController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DeleteAccountController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\ProflieController;
use App\Http\Controllers\RestController;
use App\Http\Controllers\TripAdminController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenController;
use App\Models\Activity;
use App\Http\Controllers\LocationController;

use App\Http\Controllers\PushNotificationController;



//1-owner && superAdmin.

Route::post('admin/register',[AuthenController::class,'adminRegister']);


Route::post('admin/Login',[AuthenController::class,'adminLogin']);


Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api','scope:admin'] ],function() {

    Route::post('logout',[AuthenController::class,'adminLogout']);




});



//******************************************************************************************************************


//1_Owner

Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api','scope:admin'] ],function(){


    Route::get('index1',[ProflieController::class,'index1'])->middleware('check_owner');
    Route::post('update1',[ProflieController::class,'update1'])->middleware('check_owner');
    Route::post('change1',[ProflieController::class,'updatePassword1'])->middleware('check_owner');
    Route::post('deletedAccount',[DeleteAccountController::class,'softDeleteOwner'])->middleware('check_owner');
    // Adding a trip by an admin

    Route::post('/addTripCompany',[TripAdminController::class, 'addTripWithActivities'])->middleware('check_owner');

    // get all trips

    Route::get('/getAllTrips',[TripAdminController::class, 'getAllTrips'])->middleware('check_owner');


    // get a trip details


    Route::get('/getTripDetails/{id}',[TripAdminController::class, 'getTripDetails'])->middleware('check_owner');


    // get  activities for a trip


    Route::get('/getActivityForTrip/{id}',[TripAdminController::class, 'getActivityForTrip'])->middleware('check_owner');



    // delete trip

    Route::get('/deleteTrip/{id}',[TripAdminController::class, 'deleteTrip'])->middleware('check_owner');



    //  update trip


    Route::post('/updateTrip/{tripId}',[TripAdminController::class, 'updateTrip'])->middleware('check_owner');


    //  update activity with its details


    Route::post('/updateActivity/{Id}',[TripAdminController::class, 'updateActivity'])->middleware('check_owner');


    //-----------------------------------------------------------------------

    //Add a activity

    Route::post('/AddActivityToCompany',[ActivitySuperAdminController::class, 'AddActivity'])->middleware('check_owner');

    // get all activities

    Route::get('/getAllActivitiesForCompany',[ActivitySuperAdminController::class, 'getAllActivities'])->middleware('check_owner');
    Route::get('/getCommentsForTripCompany/{tripId}',[TripAdminController::class,'getComment'])->middleware('check_owner');

    //-------------------------------------------------------------------------

//hotels

    Route::post('addHotels',[HotelController::class,'store'])->middleware('check_owner');;
    Route::post('destroyHotels/{id}',[HotelController::class,'destroy'])->middleware('check_owner');
    Route::post('updateHotels/{id}',[HotelController::class,'update'])->middleware('check_owner');
    Route::post('searchHotels',[HotelController::class,'search'])->middleware('check_owner');
    Route::get('getHotels',[HotelController::class,'index'])->middleware('check_owner');;
    Route::get('showHotels/{id}',[HotelController::class,'show'])->middleware('check_owner');

////rest
    Route::post('addRests',[RestController::class,'store'])->middleware('check_owner');
    Route::post('destroyRests/{id}',[RestController::class,'destroy'])->middleware('check_owner');
    Route::post('updateRests/{id}',[RestController::class,'update'])->middleware('check_owner');
    Route::get('showRests/{id}',[RestController::class,'show'])->middleware('check_owner');
    Route::get('getRests',[RestController::class,'index'])->middleware('check_owner');
///chat

    Route::post('/sendMessagesAdmin', [ConversationController::class,'sendMessageAdmin'])->middleware('check_owner');

    Route::get('/getMessagesAdmin/{id}', [ConversationController::class,'getMessagesAdmin'])->middleware('check_owner');
//    getAllUsersWithAdmin
    Route::get('/getAllUsersWithAdmin', [ConversationController::class,'getAllUsersWithAdmin'])->middleware('check_owner');

//   Route::get('/getcomment/{tripId}',[TripAdminController::class,'getAverageRating'])->middleware('check_owner');


Route::post('/location', [LocationController::class, 'getCoordinates'])->middleware('check_owner');;

});

//*****************************************************************************************************


//2-Super_Admin


Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api','scope:admin'] ],function() {



    Route::post('store2',[ControlpanelController::class,'store2'])->middleware('check_admin');
    Route::post('store',[ControlpanelController::class,'store'])->middleware('check_admin');
    Route::get('index',[ControlpanelController::class,'index'])->middleware('check_admin');
    Route::post('update/{id}',[ControlpanelController::class,'update'])->middleware('check_admin');
    Route::post('softDelete/{id}',[ControlpanelController::class,'softDelete'])->middleware('check_admin');
    Route::get('show/{id}',[ControlpanelController::class,'show'])->middleware('check_admin');

    Route::get('index2',[ControlpanelController::class,'index2'])->middleware('check_admin');
    Route::post('update2/{id}',[ControlpanelController::class,'update2'])->middleware('check_admin');
    Route::get('softDelete2/{id}',[ControlpanelController::class,'softDelete2'])->middleware('check_admin');
    Route::get('show2/{id}',[ControlpanelController::class,'show2'])->middleware('check_admin');



    // Wallet_charging for superAdmin.

    Route::post('/WalletCharging/{id}', [WalletController::class, 'Wallet_charging'])->middleware('check_admin');




    // Adding a trip by superAdmin

    Route::post('/addTripSuperAdmin',[TripAdminController::class, 'addTripWithActivities'])->middleware('check_admin');




//************************************************************** */

    //Add a activity

    Route::post('/AddActivity',[ActivitySuperAdminController::class, 'AddActivity'])->middleware('check_admin');


    // get all activities

    Route::get('/getAllActivities',[ActivitySuperAdminController::class, 'getAllActivities'])->middleware('check_admin');


     // update activity only name


     Route::post('/updateActivityOnlyName/{id}',[ActivitySuperAdminController::class, 'updateActivity'])->middleware('check_admin');


     // delete_Activity


     Route::delete('/delete_Activity/{id}',[ActivitySuperAdminController::class, 'delete_Activity'])->middleware('check_admin');

//----------------------------------------------------------------------------

    Route::get('/getTripDetailsforadmin/{id}',[TripAdminController::class, 'getTripDetails'])->middleware('check_admin');
    Route::get('/getCommentsForTrip/{tripId}',[TripAdminController::class,'getComment'])->middleware('check_admin');

    //**********************

// for testing
    Route::post('/send-push-notification', [PushNotificationController::class, 'sendPushNotification']);
    ///hotels
    Route::post('addHotelsForSuper',[HotelController::class,'store'])->middleware('check_admin');;
    Route::post('destroyHotelsForSuper/{id}',[HotelController::class,'destroy'])->middleware('check_admin');
    Route::post('updateHotelsForSuper/{id}',[HotelController::class,'update'])->middleware('check_admin');
    Route::post('searchHotelsForSuper',[HotelController::class,'search'])->middleware('check_admin');
    Route::get('getHotelsForSuper',[HotelController::class,'index'])->middleware('check_admin');;
    Route::post('showHotelsForSuper/{id}',[RestController::class,'show'])->middleware('check_admin');
////rest
    Route::post('addRestsForSuper',[RestController::class,'store'])->middleware('check_admin');
    Route::post('destroyRestsForSuper/{id}',[RestController::class,'destroy'])->middleware('check_admin');
    Route::post('updateRestsForSuper/{id}',[RestController::class,'update'])->middleware('check_admin');
    Route::get('getRestsForSuper',[RestController::class,'index'])->middleware('check_admin');
    Route::post('showRestsForSuper/{id}',[RestController::class,'show'])->middleware('check_admin');
});


