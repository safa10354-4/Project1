<?php

use App\Http\Controllers\profileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenController;
use App\Http\Controllers\DeleteAccountController;
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




        // Route::get('index',[profileController::class,'index']);
        Route::get('/deleted1/{id}',[DeleteAccountController::class,'softDelete']);
        Route::get('index',[profileController::class,'index']);
        Route::post('update',[profileController::class,'update']);
        Route::post('/image',[profileController::class, 'image']);
        Route::post('/change',[profileController::class, 'updatePassword']);



});
