<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;



use App\Models\Admin;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable , SoftDeletes;


    protected $fillable = [
'name', 'email', 'password', 'phone_number', 'image',
'age', 'gender', 'nationality',
];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'deleted_at' => 'datetime',
    ];



//====================================================================================================

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }



    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'bookings', 'user_id', 'trip_id');
    }







    //====================================================================================








}
