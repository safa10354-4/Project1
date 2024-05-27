<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ControlpanelController extends Controller
{

    public function index()
    {
        $posts = User::all();
        return response(['ListUsers'=> $posts]);
    }


    //==============================================================================================

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:55', 'string'],
            'email' => ['email', 'required', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone_number' => ['required', 'unique:users,phone_number', 'digits:10'],
            'age' => ['required'],
            'nationality' => ['required'],
            'gender' => ['required'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
            'nationality' => $request->nationality,
            'age' => $request->age,
            'gender' => $request->gender,
        ];
        $users = User::query()->create($data);
        $wallet = new Wallet();
        $wallet['user_id'] = $users['id'];

        $wallet['balance'] = 0;
        $wallet->save();

        //**********************************************************************


        return response()->json([

            'users' => $users,
        ], 200);}

//===================================================================================================

    public function update(Request $request, $id)
    {

        $input = $request->except('email', 'image', 'password');

        if ($request->hasFile('image')) {
            $avatarName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('avatars'), $avatarName);

            User::find($id)->update(array_merge($input, ['image' => $avatarName]));
        } else {
            User::find($id)->update($input);
        }

        return response(['message' => 'success User updated successfully.']);
    }


    public function softDelete($id)
    {
        $user= User::find($id);

        if (!$user) {
            return response(['message'=> 'Account not found']);
        }
        $user->delete();
        return response(['message' => 'Account  soft deleted']);
    }


    public function show($id)
    {
        $user = User::find($id);
        return response(['user'=>$user ]);
    }



    public function index2()
    {
        $ex = [1];
        $posts = Admin::whereNotIn('id', $ex)->get();

        return response(['ListUsers'=> $posts]);
    }

    public function store2(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:55', 'string'],
            'email' => ['email', 'required', 'unique:admins'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'description_company' => ['required'],
            'type'=>['required'],
            'company_website' => ['required'],
            'image' => ['required'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'description_company' => $request->description_company,
            'company_website' => $request->company_website,
            'type'=>$request->type,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $avatarName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $avatarName);

            // تخزين المسار الكامل للصورة
            $imagePath = 'images/' . $avatarName;

            $users = Admin::query()->create(array_merge($data, [
                'image' => $imagePath
            ]));
        } else {
            $users = Admin::query()->create($data);
        }


        return response()->json([

            'info_owner' => $users,
        ], 200);
    }

    public function update2(Request $request, $id)
    {

        $input = $request->except('email','image','password');
        Admin::find($id)->update($input);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images');

            Admin::find($id)->update(array_merge($input, ['image' => $imagePath]));}
        return response(['message'=>'success User updated successfully.']);

    }


    public function softDelete2($id)
    {

        $user= Admin::find($id);

        if (!$user) {
            return response(['message'=> 'Account not found']);
        }
        $user->delete();
        return response(['message' => 'Account  soft deleted']);
    }


    public function show2($id)
    {
        $user = Admin::find($id);
        return response(['user'=>$user ]);
    }

}
