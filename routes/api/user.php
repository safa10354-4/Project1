<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DeleteAccountController;
use App\Http\Controllers\Favcontroller;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ProflieController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\RestController;
use App\Http\Controllers\TripUserController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\UserTripController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenController;



//1-User

Route::post('user/register',[AuthenController::class, 'userRegister']);
Route::post('user/Login',[AuthenController::class, 'userLogin']);


//Reset_password
Route::post('user/password/email',[AuthenController::class,'UserForgotPassword']);
Route::post('user/password/code/check',[AuthenController::class,'UserCheckCode']);
Route::post('user/password/reset', [AuthenController::class,'UserResetPassword']);




//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



Route::group( ['prefix' => 'user','middleware' => ['auth:user-api','scopes:user'] ],function() {
    Route::post('logout', [AuthenController::class, 'userLogout']);
//profile
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
    // fetch details of a specific activity
    Route::get('/getActivityDetails/{activityId}',[TripUserController::class, 'getActivityDetails']);
    //  book a trip
    Route::post('/bookingTrip/{tripId}',[BookingController::class, 'bookTrip']);
    //Cancel your trip reservation
    Route::post('/cancelBooking/{bookingId}',[BookingController::class, 'cancelBooking']);
    //   get all my reservation for the trip
    Route::get('/getAllMyBookings',[BookingController::class, 'getAllMyBookings']);



//    commentBooking
    Route::post('/commentBooking/{id}',[BookingController::class,'storeComment']);
    Route::post('/rateBooking/{id}',[BookingController::class,'StoreReview']);
//search trip
    Route::post('search',[TripUserController::class, 'search']);
//delete Account
    Route::post('softDelete',[DeleteAccountController::class,'softDelete']);
//hotel
    Route::get('indexHotel',[HotelController::class,'index1']);
    Route::get('showHotel/{id}',[HotelController::class,'show']);
    Route::post('searchHotel',[HotelController::class,'search']);
//restaurant
    Route::get('indexRest',[RestController::class,'index1']);
    Route::post('searchRest',[RestController::class,'search']);
    Route::get('showRest/{id}',[RestController::class,'show']);
//favorites
    Route::post('/Favorites', [Favcontroller::class, 'addFavorite']);
    Route::get('/getFav', [Favcontroller::class, 'getFavorites']);
    Route::post('/removeFav', [Favcontroller::class, 'removeFavorite']);

//chat
    Route::post('/sendMessages', [ConversationController::class,'sendMessage']);
    Route::get('/getMessages/{id}', [ConversationController::class,'getMessages']);



    //UserTrip
//  Route::get('/ShowTrips/{id}', [UserTripController::class,'show']);
    Route::get('/indexTrips', [UserTripController::class,'index']);
    Route::post('/StoreTrips', [UserTripController::class,'store']);
    Route::post('/UpdateTrips/{id}', [UserTripController::class,'update']);
    Route::post('/DestroyTrips/{id}', [UserTripController::class,'destroy']);
    //ActivityTrip
    Route::get('/ShowAct/{id}', [UserActivityController::class,'show']);
    Route::post('/StoreAct', [UserActivityController::class,'store']);
    Route::post('/UpdateAct/{id}', [UserActivityController::class,'update']);
    Route::post('/DestroyAct/{id}', [UserActivityController::class,'destroy']);



    //create_device_token
    Route::post('/create_device_token', [PushNotificationController::class, 'create_device_token']);



});
