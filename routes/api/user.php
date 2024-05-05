<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProflieController;
use App\Http\Controllers\TripUserController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



//1-User

Route::post('user/register',[AuthenController::class, 'userRegister']);
Route::post('user/Login',[AuthenController::class, 'userLogin']);



Route::group( ['prefix' => 'user','middleware' => ['auth:user-api','scopes:user'] ],function() {

       Route::post('logout', [AuthenController::class, 'userLogout']);




    Route::get('index',[ProflieController::class,'index']);
    Route::post('update',[ProflieController::class,'update']);
    Route::post('change',[ProflieController::class,'updatePassword']);
    Route::post('image',[ProflieController::class,'store1']);







    // get all trips

    Route::get('/getValidTrips',[TripUserController::class, 'getValidTrips']);


      //   get all optional  activities
    Route::get('/getAllActivitiesOptional/{id}',[TripUserController::class, 'getAllActivitiesOptional']);


    //   get all  non optional  activities
    Route::get('/getAllActivities_Non_Optional/{id}',[TripUserController::class, 'getAllActivities_Non_Optional']);


    //$tripId


    Route::post('/bookingTrip/{tripId}',[BookingController::class, 'bookTrip']);



});
