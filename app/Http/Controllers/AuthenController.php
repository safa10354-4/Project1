<?php

namespace App\Http\Controllers;

use App\Mail\SendCodeResetPassword;
use App\Models\Admin;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\ResetCodePassword;
class AuthenController extends Controller
{
    public function userRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:55', 'string'],
            'email' => ['email', 'required', 'unique:users'],
            'password' => ['required','confirmed', Password::defaults()],

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


        //********************************************************

        //Create the wallet for the user
        $wallet = new Wallet();
        $wallet['user_id'] = $user['id'];

        $wallet['balance'] = 0;
        $wallet->save();


        //**********************************************************************



        return response([
            'user' => $user,
            'access_token' => $accessToken
        ]);
    }




    //=============================================================================================


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
        }

    }


    //===================================================================================================

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

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'description_company' => $request->description_company,
            'company_website' => $request->company_website,
        ];

//

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $avatarName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $avatarName);

            // تخزين المسار الكامل للصورة
            $imagePath = 'images/' . $avatarName;

            $admin = Admin::query()->create(array_merge($data, [
                'image' => $imagePath
            ]));

            $accessToken = $admin->createToken('MyApp',['admin'])->accessToken;

            return response()->json([
                'message' => 'تم تحميل الصورة بنجاح',
                'admin' => $admin,
                'access_token' => $accessToken
            ], 200);
        } else {
            return response()->json(['message' => 'الرجاء إرفاق ملف صورة'], 400);
        }


    }


    //********************************************************************************************

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




    //*****************************************************************************************


    public function adminLogout()
    {
        Auth::guard('admin-api')->user()->token()->revoke();
        return response()->json(['success' => 'logged out successfully']);
    }



    //***********************************************************************************************

    public function UserForgotPassword(Request $request){


        $data=$request->validate([

            'email' => ['required', 'email', 'exists:users']
        ]);


        // Delete all old code that user send before.

        ResetCodePassword::query()->where('email', $request['email'])->delete();

        //Generate random code

        $data['code']=mt_rand(100000,999999);

        //Create a new code

        $codeData=ResetCodePassword::query()->create($data);

        // Send email to user.


        Mail::to($request['email'])->send(new SendCodeResetPassword($codeData['code']));

        return response(['message' => trans('code.sent')]);
    }



//========================================================================================================================


    public function UserCheckCode(Request $request){
        $request->validate([
            'code' => ['required','string','exists:reset_code_passwords'],
        ]);

        // find the code
        $passwordReset = ResetCodePassword::query()->firstWhere('code', $request['code']);

        // check if it does not expired: the time is one hour
        if ($passwordReset['created_at'] > now()->addHour()) {
            $passwordReset->delete();
            return response(['message' => trans('passwords.code_is_expire')], 422);
        }

        return response([
            'code' => $passwordReset['code'],
            'message' => trans('passwords.code_is_valid')
        ]);
    }



    //===============================================================================================


    public function UserResetPassword(Request $request){


        $input=$request->validate([
            'code' => ['required','string','exists:reset_code_passwords'],
            'password' => ['required','confirmed']
        ]);

        // find the code
        $passwordReset = ResetCodePassword::query()->firstWhere('code', $request['code']);

        // check if it does not expired: the time is one hour
        if ($passwordReset['created_at'] > now()->addHour()) {
            $passwordReset->delete();
            return response(['message' => trans('passwords.code_is_expire')], 422);
        }

// find user's email
        $user = User::query()->firstWhere('email', $passwordReset['email']);



// update user password
        $input['password']=bcrypt($input['password']);
        $user->update([

            'password'=>$input['password'],
        ]);



// delete current code
        $passwordReset->delete();

        return response(['message' =>'password has been successfully reset']);
    }











}
