<?php

namespace App\Http\Controllers;
use App\Models\Hotel;
use App\Models\Rest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RestController extends Controller
{
    //Admin
    public function index()
    {

        $restaurants=Rest::where('admin_id',Auth::id())->get();

        return response(['restaurants'=>$restaurants]);
    }

    //user
//    public function index1()
//    {
//        $restaurants = Rest::all();
//        return response(['restaurants'=>$restaurants]);
//    }
    public function index1()
    {
        $userId = auth()->id();
        $Rests = Rest::with('favorites')->get();

        foreach ($Rests as $Rest) {
            // Check if the hotel is a favorite for the authenticated user
            $Rest->is_favorite = $Rest->favorites->contains('user_id', $userId);
            // Remove the favorites relationship from the response
            unset($Rest->favorites);
        }

        return response()->json(['Rests' => $Rests], 200);
    }




    public function show($id)
    {
        $rest= Rest::find($id);
        if (!$rest) {
            return response(['message' => 'rest not found'], 404);
        }
        $rests = Rest::where('id',$id)->first();

        return response(['messege'=>$rests],200);
    }


   //***


    public function search(Request $request){
        $userId = auth()->id();
        // Get the search value from the request
        $search = $request->input('search');

        $Rests = Rest::query()
            ->where('name', 'LIKE', "%{$search}%")
            ->orWhere('location', 'LIKE', "%{$search}%")->orWhere('table_count','LIKE', "%{$search}%")
            ->get();
        foreach ($Rests as $Rest) {
            // Check if the hotel is a favorite for the authenticated user
            $Rest->is_favorite = $Rest->favorites->contains('user_id', $userId);
            // Remove the favorites relationship from the response
            unset($Rest->favorites);
        }
        return response(['message'=> $Rests ]);}

    //****

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'location' => 'required',
            'table_count' => 'required|integer',
            'type' => 'required',
            'description' => 'required',
            'image' => 'required|image',
            'rate'=> 'required',
        ]);

        $restaurantData = [
            'admin_id' => Auth::id(),
            'name' => $validatedData['name'],
            'location' => $validatedData['location'],
            'description' => $validatedData['description'],
            'table_count' => $validatedData['table_count'],
            'type' => $validatedData['type'],
            'rate' =>$validatedData['rate'],
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $avatarName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $avatarName);
            $imagePath = 'images/' . $avatarName;
            $restaurantData['image'] = $imagePath;
        }

        $rests = Rest::create($restaurantData);

        return response(['message' => 'Restaurant created successfully', 'restaurant' => $rests], 201);
    }


    public function update(Request $request, $id)
    {
        $rest = Rest::find($id);
        if (!$rest) {
            return response(['message' => 'Restaurant not found'], 404);
        }


        $data = $request->except('image');


        $rest->update($data);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $avatarName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $avatarName);
            $imagePath = 'images/' . $avatarName;

            $rest->update(['image' => $imagePath]);
        }

        return response(['success', 'Restaurant updated successfully.']);
    }
    public function destroy($id)
    {
        $rest= Rest::find($id);
        if (!$rest) {
            return response(['message' => 'rest not found'], 404);
        }
        $rest=Rest::query()->find($id);
        $rest->delete();


        return response(['success', 'Restaurant deleted successfully.']);
    }
}
