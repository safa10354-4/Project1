<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Foundation\Auth\Admin as Authenticatable;
 //use Illuminate\Foundation\Auth\User
class Admin extends Authenticatable
{
    use HasFactory,HasFactory, HasApiTokens;

    protected $fillable = ['name',
'email', 'password',
'description_company', 'type', 'company_website', 'image',];

    protected $hidden = [
        'password',
        'remember_token',
    ];

}
