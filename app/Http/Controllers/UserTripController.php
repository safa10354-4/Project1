<?php

namespace App\Http\Controllers;

use App\Models\Rest;
use App\Models\UserTrip;
use Illuminate\Http\Request;

class UserTripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        $restaurants = Rest::all();
        $trip = UserTrip::all();
        return response([ $trip]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $name = $request->input('name');
        $description = $request->input('description');
        $flight_date = $request->input('flight_date');
        $flight_duration=$request->input('flight_duration');

        $trip = UserTrip::create([
            'name' => $name,
            'description' => $description,
            'flight_duration' =>  $flight_duration,
     'flight_date' => $flight_date,
        ]);

        return response([ $trip]);
    }

    /**
     * Display the specified resource.
     */
//    public function show()
//    {
//        // جلب الرحلة
//        $trip = UserTrip::findOrFail($tripId);
//
//        // تحميل الأنشطة المرتبطة بالرحلة
//        $trip->load('activities');
//
//        // إعادة النتيجة إلى العرض (view) أو كـ JSON
//        return response([$trip]);
//        // أو إذا كنت ترغب في إرجاع البيانات كـ JSON
//        // return response()->json($trip);
//    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $tripId)
    {
        $data = $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'flight_duration' => 'nullable',
            'flight_date' => 'nullable',

        ]);

        $trip = UserTrip::findOrFail($tripId);

        $trip->update($data);

        return response()->json(["message" => "Update successful", 'tripAfterUpdate'=>$trip],200);
    }


    public function destroy($tripId)
    {
        try {
            $trip = UserTrip::findOrFail($tripId);
            $trip->delete();

            return response("Done deleted successful", 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response("Not found trip", 404);
        }
    }

}
