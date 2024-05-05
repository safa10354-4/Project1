<?php

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

    Route::post('/addTrip',[TripAdminController::class, 'addTripWithActivities'])->middleware('check_owner');

    // get all trips with activities

    Route::get('/getAllTripsWithActivities',[TripAdminController::class, 'getAllTripsWithActivities'])->middleware('check_owner');



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

});

