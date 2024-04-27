<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class profileController extends Controller
{
    public function index()
    {
        $current_userid = Auth::id();
        $userprofile = User::where('id', '=', ' $current_userid')->first();

        return response([
            'current_userid' => $current_userid,
            'userprofile' => $userprofile
        ]);
    }

    public function index1()
    {
        $current_userid = Auth::id();
        $userprofile = DB::table('admins')->where('id', '=', $current_userid)->first();

        return response([
            'current_userid' => $current_userid,
            'userprofile' => $userprofile
        ]);
    }

    public function update(Request $request)
    {
//

        $input = User::all()->except(['email']);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images');
            $input['image'] = $imagePath ;

        } else {
            unset($input['image']);
        }

        if ($request->filled('password')) {
            $input['password'] = Hash::make($input['password']);

        } else {
            unset($input['password']);
        }

        User::find(Auth::id())->update($input);

        return response(['message'=>'success Profile updated successfully.']);
    }

    public function update1(Request $request)
    {
//
       // $input = Admin::all()->except(['email']);
        $input = $request->except('email');
  // $input = $request->all();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images');
            $input['image'] = $imagePath ;

        } else {
            unset($input['image']);
        }


        if ($request->filled('password')) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
       Admin::find(Auth::id())->update($input);

        return response(['message'=>'success Profile updated successfully.']);


    }


    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // التحقق من صحة كلمة المرور الحالية
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        // تحديث كلمة المرور للمستخدم
        $user->password = bcrypt($request->password); // تخزين كلمة المرور المشفرة
        $user->save();

        return redirect()->back()->with('success', 'تم تحديث كلمة المرور بنجاح.');
    }


    public function image(Request $request){
    $data = ['image' => $request->image];

        $user_id=Auth::id();
     $admin = User::updateOrCreate(['id' => $user_id], $data );

    if ($request->hasFile('image')) {

        $imagePath = $request->file('image')->store('images');
        return response()->json(['message' => 'تم تحميل الصورة بنجاح', 'image_path' => $imagePath] );
    } else {
        return response()->json(['message' => 'الرجاء إرفاق ملف صورة'], 400);
    }}
public function updatePassword1(Request $request)
{
    # Validation
    $request->validate([
    'old_password' => 'required',
    'new_password' => 'required|confirmed',
]);


    #Match The Old Password
    if(!Hash::check($request->old_password, auth()->user()->password)){
        return response("error", "Old Password Doesn't match!");
    }


    #Update the new Password
    User::whereId(auth()->user()->id)->update([
        'password' => Hash::make($request->new_password)
    ]);

    return back()->with("status", "Password changed successfully!");
}}


