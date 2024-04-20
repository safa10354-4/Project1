<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class profileController extends Controller
{
    public function index()
    {
        $current_userid=Auth::user();
        $userprofile=User::where('id','=',' $current_userid')->select('name', 'email', 'password', 'phone_number', 'confirm_password', 'image',
            'age', 'gender', 'nationality')->first();

              return response([
                  'current_userid' => $current_userid,
                  'userprofile' =>$userprofile
              ]);
    }
    public function index1()
    {
      //  $current_userid=Auth::user();
        $current_userid = Auth::id(); // الحصول على معرف المستخدم الحالي

        $userprofile=Admin::where('id','=',' $current_userid')->select('name',
            'email', 'password', 'confirm_password',
            'description_company', 'type', 'company_website', 'image'
           )->first();

        return response([
            'current_userid' => $current_userid,
            'userprofile' =>$userprofile
        ]);
    }


}
