<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
class AuthenController extends Controller
{
    public function userRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:55', 'string'],
            'email' => ['email', 'required', 'unique:users'],
            'password' => ['required','confirmed', Password::defaults()],
          //  'phone_number' =>  ['required'],
            'phone_number'=>['required','unique:users,phone_number','digits:10'],
            'age' =>  ['required'],
            'nationality' =>['required'] ,
            'gender' => ['required'],
        ]);

        $user = \App\Models\User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
            'age' => $request->age,
            'nationality' => $request->nationality,
            'gender' => $request->gender,
        ]);
        $accessToken = $user->createToken('MyApp',['user'])->accessToken;

        return response([
            'user' => $user,
            'access_token' => $accessToken
        ]);
}
    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password'=>'required'
        ]);
        $credentials = request(['email', 'password']);


        if(auth()->guard('user')->attempt($request->only('email','password'))){

            config(['auth.guards.api.provider' => 'user']);

            $user = User::query()->select('users.*')->find(auth()->guard('user')->user()['id']);
            $success =  $user;
            $success['token'] =  $user->createToken('MyApp',['user'])->accessToken;

            return response()->json($success, 200);
        }
        else{
            return response()->json(['error' => ['Unauthorized']], 401);
        }}

        public function userLogout()
    {
            Auth::guard('user-api')->user()->token()->revoke();
            return response()->json(['success'=>'logged out successfully']);
    }

    public function adminRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:55', 'string'],
            'email' => ['email', 'required', 'unique:admins'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'description_company' => ['required'],
            //  'type'=>['required'],
            'company_website' => ['required'],
            'image' => ['required'],
        ]);


        $admin = Admin::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'description_company' => $request->description_company,
            'company_website' => $request->company_website,

            'image' => $request->image,

        ]);
        $accessToken = $admin->createToken('MyApp',['admin'])->accessToken;

        if ($request->hasFile('image')) {

            $imagePath = $request->file('image')->store('images');


            return response()->json(['message' => 'تم تحميل الصورة بنجاح', 'image_path' => $imagePath,   'admin' => $admin,
                'access_token' => $accessToken], 200);
        } else {
            // إذا لم يتم إرفاق ملف صورة في الطلب
            return response()->json(['message' => 'الرجاء إرفاق ملف صورة'], 400);
        }}



        //$accessToken = $admin->createToken('MyApp',['admin'])->accessToken;

     //   $admin['remember_token'] = $accessToken;
//        return response([
//            'admin' => $admin,
//            'access_token' => $accessToken
//        ]);}

    public function adminLogin(Request $request)
    {
       $request->validate([
           'email' =>'required|email',
           'password' => 'required'
        ]);
        $credentials = request(['email', 'password']);
//        $credentials['active']=1;
//        $credentials['deleted_at']=null;

        if (auth()->guard('admin')->attempt($request->only('email', 'password'))) {
            config(['auth.guards.api.provider' =>'admin']);
            $admin = Admin::query()->select('admins.*')->find(auth()->guard('admin')->user()['id']);

            $success =$admin;

            $success['token'] =$admin->createToken('MyApp',['admin'])->accessToken;

            return response()->json($success);
        } else {
            return response()->json(['error' => ['Unauthorized.']], 422);
        }
    }
    public function adminLogout()
    {
        Auth::guard('admin-api')->user()->token()->revoke();
        return response()->json(['success' => 'logged out successfully']);
    }
}
//         $admin = Admin::query()->create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'password' => bcrypt($request->password),
//             'description_company' => $request->description_company,
//             'company_website' => $request->company_website,
//             'image' => $request->image,
//         ]);
//        if($request->hasFile('image')) {
//            $image = $request->file('image')->store('public');

//        if($request->hasfile('image')) {
//            $file = $request->file('image');
//            $extension = $file->getClientOriginalExtension();
//            $filename = time() . '.' . $extension;
//

//            if ($file->move('uploads/images/', $filename)) {
//
//                $admin->image = $filename;
//            } else {
//                return response()->json(['error' => 'فشل في تحميل الصورة'], 500);
//            }
//        } else {
//            $admin->image = '';
//        }
//
//// حفظ التغييرات في قاعدة البيانات
//        $admin->save();

//        if($request->hasFile('image')) {
//            $image = $request->file('image')->store('public');
//            $image=Storage::disk('public')->put('/',$request->file('image'));}
