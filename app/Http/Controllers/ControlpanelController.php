<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ControlpanelController extends Controller
{



    public function index()
    {
//        $ex = [1];
//        $posts = User::whereNotIn('id', $ex)->get();
        $posts = User::all();
        return response(['ListUsers'=> $posts]);
    }

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
           //'image' => ['required'],
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
//        if ($request->hasFile('image')) {
//            $image = $request->file('image');
//            $imageName = $image->hashName();
//
//            Storage::disk("public")->put($imageName, file_get_contents($image));

//            $users = User::query()->create(array_merge($data, [
//                'image' => $imageName
//            ]));


     $users = User::query()->create($data);

            $accessToken = $users->createToken('MyApp', ['admin'])->accessToken;

            return response()->json([
               // 'message' => 'تم تحميل الصورة بنجاح',
                'users' => $users,
                'access_token' => $accessToken
            ], 200);}

//        } else {
//            return response()->json(['message' => 'الرجاء إرفاق ملف صورة'], 400);
//        }}

    public function update(Request $request, $id)
    {

//        $post = User::find($id);
//        $post->update($request->all());
//        return
//           response('success', 'User updated successfully.');


        $input = $request->except('email','image','password');
        User::find($id)->update($input);
        if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = $image->hashName();

        Storage::disk("public")->put($imageName, file_get_contents($image));
        // Admin::find(Auth::id())->update($input);

        User::find($id)->update(array_merge($input, ['image' => $imageName]));}
        return response(['message'=>'success User updated successfully.']);

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


//    public function edit($id)
//    {
//        $post = User::find($id);
//        return view('posts.edit', compact('post'));
//    }


    public function index2()
    {
        $ex = [1];
        $posts = User::whereNotIn('id', $ex)->get();
//        $posts = Admin::all();
        return response(['ListUsers'=> $posts]);
    }

    public function store2(Request $request)
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

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();

            Storage::disk("public")->put($imageName, file_get_contents($image));

            $users = Admin::query()->create(array_merge($data, [
                'image' => $imageName
            ]));

            $accessToken = $users->createToken('MyApp',['admin'])->accessToken;

            return response()->json([
                'message' => 'تم تحميل الصورة بنجاح',
                'users' => $users,
                'access_token' => $accessToken
            ], 200);
        } else {
            return response()->json(['message' => 'الرجاء إرفاق ملف صورة'], 400);
        }}

    public function update2(Request $request, $id)
    {

//        $post = User::find($id);
//        $post->update($request->all());
//        return
//           response('success', 'User updated successfully.');


        $input = $request->except('email','image','password');
      Admin::find($id)->update($input);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();

            Storage::disk("public")->put($imageName, file_get_contents($image));
            // Admin::find(Auth::id())->update($input);

            Admin::find($id)->update(array_merge($input, ['image' => $imageName]));}
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


    public function edit2($id)
    {
        $post = Admin::find($id);
        return view('posts.edit', compact('post'));
    }








}
