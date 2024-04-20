<?php

namespace App\Http\Controllers;

//use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([

            'name' => ['required', 'max:55', 'string'],
            'email' => ['email', 'required', 'unique:users'],
            'password' => ['required', Password::defaults()],
        ]);

        $user = \App\Models\User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Registeration failed'
            ]);
        }
        $accessToken = $user->createToken('personal Access Token')->accessToken;
        $user['remember_token'] = $accessToken;
        return response([
            'user' => $user,
            'access_token' => $accessToken
        ]);
    }
        public function login(Request $request){
            $loginData=$request->validate([
                'email'=>'email|required|exists:users',
                'password'=>'required'
            ]);
            if(!auth()->attempt($loginData)){
                return response()->json([
                    'errors'=>[
                        'message'=>['could not sign you in with those credentials']]
                    ],422);
    }
            $user=$request->user();
            $accessToken=$user->createToken('personal Access Token');
            $user['remember_token']= $accessToken;

            $accessToken->token->save();
            return response()->json([
                'user'=>$user,
                'access_token'=>$accessToken->accessToken,
            ]);
    }
public function logout(){
        $user=Auth::user()->token()->revoke();
        return response()->json(['success'=>'logged out successsfully'],200);
}
}



