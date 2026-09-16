<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{

    use HasFactory;
    protected $fillable = [
        'name', 'location', 'type',  'table_count', 'rate',
'description','admin_id','image'

        // 'rates'
    ];



    //=================================================================================================


    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }


//    public function favorites()
//    {
//        return $this->morphMany(Favorite::class, 'favoritable');
//    }
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}
