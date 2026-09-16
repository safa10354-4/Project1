<?php

namespace App\Http\Controllers;
use App\Models\Favorite;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{

//Admin
    public function index()
    {


//        $restaurants = Rest::all();
        $hotels=Hotel::where('admin_id',Auth::id())->get();

        return response(['hotels'=>$hotels]);
    }
    //user
//   public function index()
//    {
//
//        $hotels = Hotel::all();
//        foreach ( $hotels as  $hotel) {
//            $hotel->update([
//                'key'=>0
//            ]);
//
//            $hotel->save();
//        }
//        $user=Auth::id();
//
////        $favorites = $user->favorites()->with('favoritable')->where('favoritable_type','App\Models\Hotel')->get();
//        $favorites =Favorite::query()->where('favoritable_type','App\Models\Hotel')->where('user_id',$user)->get();
//
//
//        foreach ($favorites as $favorite) {
//
//           $gg= Hotel::query()->where('id',$favorite['id']);
//            $gg->update([
//                'key'=>1
//            ]);
//
//            $gg->save();
//           }

//        $hotels = Hotel::all();

//        return response(['message' => $hotels], 200);
//
//    }
//    public function index1()
//    {
//        $userId = Auth::id();
//        $hotels = Hotel::all();
//
//        // جلب المفضلات للمستخدم الحالي
//        $favorites = Favorite::where('favoritable_type', 'App\Models\Hotel')
//            ->where('user_id', $userId)
//            ->pluck('hotel_id') // استخدم trip_id للإشارة إلى الفندق المفضل
//            ->toArray();
//
//        // تعيين حالة المفضلة لكل فندق
//        foreach ($hotels as $hotel) {
//            $hotel->is_favorite = in_array($hotel->id, $favorites);
//        }
//
//        return response(['hotels' => $hotels], 200);
//    }
    public function index1()
    {
        $userId = auth()->id();
        $hotels = Hotel::with('favorites')->get();

        foreach ($hotels as $hotel) {
            // Check if the hotel is a favorite for the authenticated user
            $hotel->is_favorite = $hotel->favorites->contains('user_id', $userId);
            // Remove the favorites relationship from the response
            unset($hotel->favorites);
        }

        return response()->json(['hotels' => $hotels], 200);
    }


    public function show($id)
    {
        $hotels = Hotel::where('id', $id)->first();
        return response(['message' => $hotels], 200);
    }

    public function store(Request $request)
    {
        $validatedata = $request->validate([
            'name' => 'required',
            'location' => 'required',
            'room_count' => 'required|integer',
            'description' => 'required',
            'rate'=>'required',
            'image' => 'required|image'
        ]);

        $hotelData = [
            'admin_id' => Auth::user()->id,
            'name' => $validatedata['name'],
            'location' => $validatedata['location'],
            'description' => $validatedata['description'],
            'room_count' => $validatedata['room_count'],
            'rate' => $validatedata['rate'],
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $avatarName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $avatarName);


            $imagePath = 'images/' . $avatarName;
            $hotels = Hotel::query()->create(array_merge($hotelData, [
                'image' =>  $imagePath
            ]));}
        $hotels->refresh();

        return response(['message' => 'Success, Hotel created successfully.','hotel'=>$hotels], 200);
    }

   //***

    public function search(Request $request)
    {
        $userId = auth()->id();
        $search = $request->input('search');
        $hotels = Hotel::query()
            ->where('name', 'LIKE', "%{$search}%")
            ->orWhere('location', 'LIKE', "%{$search}%")
            ->orWhere('room_count', 'LIKE', "%{$search}%")
            ->get();
        foreach ($hotels as $hotel) {
            // Check if the hotel is a favorite for the authenticated user
            $hotel->is_favorite = $hotel->favorites->contains('user_id', $userId);
            // Remove the favorites relationship from the response
            unset($hotel->favorites);
        }

        return response()->json(['hotels' => $hotels], 200);}
    //****

    public function update(Request $request, $id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return response(['message' => 'Hotel not found'], 404);
        }


        $data = $request->except('image');


        $hotel->update($data);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $avatarName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $avatarName);
            $imagePath = 'images/' . $avatarName;

            $hotel->update(['image' => $imagePath]);
        }

        return response(['message' => 'Update successful', 'hotel' => $hotel]);
    }

    public function destroy($id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return response(['message' => 'Hotel not found'], 404);
        }
        $hotel->delete();

        return response(['message' => 'Deletion successful']);
    }

    public function favorite($product_id)
    {
        $wish = Favorite::where('hotel_id', $product_id)->where('user_id', Auth::id())->first();
        if ($wish) {
            return response(['message' => 'Already in favorites'], 200);
        } else {

            Favorite::create([
                'user_id' => Auth::id(),
                'hotel_id' => $product_id,
            ]);
            return response(['message' => 'Added to favorites']);
        }
    }}
