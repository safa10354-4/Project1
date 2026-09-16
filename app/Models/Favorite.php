<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

//    protected $table = 'favorites';

    // App/Models/Favorite.php
    // Assuming your table is named 'favorites'
    protected $fillable = [
        ' user_id', 'favoritable_id', 'favoritable_type',
        // 'rates'
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
//    public function admin()
//    {
//        return $this->belongsTo(Admin::class);
//    }
    public function u()
    {
        return $this->belongsTo(User::class);
    }


    // Define the relationship with the Hotel, Rest, and Trip models (if needed)


    public function favoritable()
    {
        return $this->morphTo();
    }




}
