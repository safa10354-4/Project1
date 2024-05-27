<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class ProflieController extends Controller

{
    public function index()
    {
        $current_userid = Auth::id();
        $userprofile = User::where('id','=',$current_userid)->first();

        return response([
            'userprofile' => $userprofile
        ]);
    }

    public function index1()
    {
        $current_userid = Auth::id();
        $userprofile = Admin::where('id', '=', $current_userid)->first();

        return response([
            'userprofile' => $userprofile
        ]);
    }
    public function update(Request $request)
    {

        $input = $request->except('email','image','password','phone_number');
        User::find(Auth::id())->update($input);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images');

            User::find(Auth::id())->update(array_merge($input, ['image' =>$imagePath  ]));}
        return response(['message'=>'success Profile updated successfully.']);

    }


    public function update1(Request $request)
    {

        $input = $request->except('email','image','password');

        Admin::find(Auth::id())->update($input);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images');

            Admin::find(Auth::id())->update(array_merge($input, [  'image' => $imagePath ]));
        }


        return response(['message'=>'success Profile updated successfully.']);


    }


    public function updatePassword1(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        if (!Hash::check($request->current_password, $user->password)) {
            return response(['message'=>'failed']);
        }
        // Current password and new password same
        if (strcmp($request->get('current_password'), $request->password) == 0)
        {

            return response(['message'=>'error New Password cannot be same as your current password']);
        }

        $rr= Admin::find(Auth::id());

        $rr->password = bcrypt($request->password);

        $rr->save();
        return response(['message'=>'success']);
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        if (!Hash::check($request->current_password, $user->password)) {
            return response(['message'=>'failed'],401);
        }
        if (strcmp($request->get('current_password'), $request->password) == 0)
        {

            return response(['message'=>'error New Password cannot be same as your current password'],401);
        }

        $rr= User::find(Auth::id());

        $rr->password = bcrypt($request->password);

        $rr->save();
        return response(['message'=>'success']);
    }




//    public function store(Request $request)
//    {
////
//
//
//        if ($request->hasFile('image')) {
//            $image = $request->file('image');
//            $avatarName = time() . '.' . $image->getClientOriginalExtension();
//            $image->move(public_path('images'), $avatarName);
//
//            // تخزين المسار الكامل للصورة
//            $imagePath = 'images/' . $avatarName;
//
//
//        if ($request->filled('password')) {
//            $input['password'] = Hash::make($input['password']);
//        } else {
//            unset($input['password']);
//        }
//
//        User::find($id)->update($input);
//
//        return response(['message'=>'success Profile updated successfully.']);
//    }

    public function store1(Request $request)

    {

        $request->validate(['image' => 'required|image',

        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $avatarName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $avatarName);

            // تخزين المسار الكامل للصورة
            $imagePath = 'images/' . $avatarName;
            $user=Auth()->user();

            $user->where('id','=',Auth::id())->update(['image' => $imagePath]);

            return response(['massage'=>'success']);

        }

    }}
