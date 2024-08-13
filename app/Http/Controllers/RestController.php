<?php

namespace App\Http\Controllers;
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
    public function index1()
    {
        $restaurants = Rest::all();
        return response(['restaurants'=>$restaurants]);
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


    public function search(Request $request){
        // Get the search value from the request
        $search = $request->input('search');
//$p=Rest::where->('admin_id',Auth::id());
        // Search in the title and body columns from the posts table
        $rests = Rest::query()
            ->where('name', 'LIKE', "%{$search}%")
            ->orWhere('location', 'LIKE', "%{$search}%")->orWhere('table_count','LIKE', "%{$search}%")
            ->get();

        // Return the search view with the resluts compacted
        return response(['message'=> $rests ]);
    }

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

