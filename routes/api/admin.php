<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ControlpanelController;
use App\Http\Controllers\DeleteAccountController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\ProflieController;
use App\Http\Controllers\TripAdminController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenController;
use App\Models\Activity;

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

    // Adding a trip by an admin

    Route::post('/addTripCompany',[TripAdminController::class, 'addTripWithActivities'])->middleware('check_owner');

    // get all trips

    Route::get('/getAllTripsCompany',[TripAdminController::class, 'getAllTrips'])->middleware('check_owner');


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

        Route::post('/AddActivity/company',[ActivityController::class, 'AddActivity'])->middleware('check_owner');


        // get all activities

        Route::get('/getAllActivities/company',[ActivityController::class, 'getAllActivities'])->middleware('check_owner');


         // update name an activity


         Route::post('/updateActivity2/company/{id}',[ActivityController::class, 'updateActivity'])->middleware('check_owner');


         // delete_Activity


         Route::delete('/delete_Activity/company/{id}',[ActivityController::class, 'delete_Activity'])->middleware('check_owner');

    //----------------------------------------------------------------------------






    Route::get('/getAverage/{tripId}',[TripAdminController::class, 'getAverageRating'])->middleware('check_owner');




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

    Route::post('/addTrip',[TripAdminController::class, 'addTripWithActivities'])->middleware('check_admin');




//************************************************************** */

    //Add a activity

    Route::post('/AddActivity',[ActivityController::class, 'AddActivity'])->middleware('check_admin');


    // get all activities

    Route::get('/getAllActivities',[ActivityController::class, 'getAllActivities'])->middleware('check_admin');


     // update activity


     Route::post('/updateActivity2/{id}',[ActivityController::class, 'updateActivity'])->middleware('check_admin');


     // delete_Activity


     Route::delete('/delete_Activity/{id}',[ActivityController::class, 'delete_Activity'])->middleware('check_admin');

//----------------------------------------------------------------------------


});
