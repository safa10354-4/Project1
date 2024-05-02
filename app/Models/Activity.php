<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;






    protected $fillable = [

        'name_activity',
        ];

    //==============================================================================================





    public function trips()
    {
        return $this->belongsToMany(Trip::class,'activity_trips');
    }





}
