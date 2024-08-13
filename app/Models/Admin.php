<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;


use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use HasFactory,HasFactory, HasApiTokens,SoftDeletes;

    protected $fillable = ['name',
'email', 'password',
'description_company', 'type', 'company_website', 'image','role'];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];



    //====================================================================



    public function trips()
    {
        return $this->hasMany(Trip::class);
    }


    public function hotels()
    {
        return $this->hasMany('App\Hotel');
    }

    public function rests()
    {
        return $this->hasMany('App\Rest');
    }


    public function conversation(){

        return $this->hasMany(Conversation::class);

    }
    public function getIsAdminAttribute()
    {
        return $this->role === 'admin'; // أو أي طريقة أخرى تستخدمها لتحديد ما إذا كان المستخدم هو إداري
    }


//===========================================================================








//===========================================================================





}
