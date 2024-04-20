<?php

use App\Http\Controllers\profileController;
use Illuminate\Http\Request;
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

Route::post('admin/register',[AuthenController::class,'adminRegister'])->name('adminRegister');

Route::post('admin/Login',[AuthenController::class,'adminLogin'])->name('adminLogin');

Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api','scope:admin'] ],function(){
    // authenticated staff routes here

    Route::post('logout',[AuthenController::class,'adminLogout']);
   // Route::get('index1',[profileController::class,'index1']);
});
//Route::get('index1',[profileController::class,'index1']);
