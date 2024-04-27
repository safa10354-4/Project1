<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::post('login',[AuthController::class,'login']);
Route::post('register',[AuthController::class,'register']);
Route::middleware('auth:api')->group(function () {
    Route::post('details',[AuthController ::class,'details']);
    Route::post('logout',[AuthController ::class,'logout']);
});
