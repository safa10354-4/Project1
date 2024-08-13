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
    public function index1()
    {
        $hotels = Hotel::all();
        return response(['message' => $hotels], 200);
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

    public function search(Request $request)
    {
        $search = $request->input('search');

        $hotels = Hotel::query()
            ->where('name', 'LIKE', "%{$search}%")
            ->orWhere('location', 'LIKE', "%{$search}%")
            ->orWhere('room_count', 'LIKE', "%{$search}%")
            ->get();

        return response()->json($hotels);
    }

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

