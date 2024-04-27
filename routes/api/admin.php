<?php

use App\Http\Controllers\DeleteAccountController;
use App\Http\Controllers\profileController;
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


    Route::get('/deleted2/{id}',[DeleteAccountController::class,'softDeletweb']);

    Route::get('index1',[profileController::class,'index1']);
    Route::post('update1',[profileController::class,'update1']);
    Route::post('/change1',[profileController::class, 'updatePassword1']);

})->middleware('check_owner');

//*****************************************************************************************************


//2-Super_Admin


Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api','scope:admin'] ],function() {


    // Wallet_charging for superAdmin.

    Route::post('/WalletCharging/{id}',[WalletController::class, 'Wallet_charging']);




})->middleware('check_admin');
