<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{

    use HasFactory;
    protected $fillable = [
        'name', 'location',  'room_count', 'rate','description','admin_id','image'
    ];



    //=================================================================================================


    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

}
