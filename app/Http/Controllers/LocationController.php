<?php

namespace App\Http\Controllers;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getCoordinates(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string',
            'province' => 'required|string',
        ]);

        $location = Location::where('country', $validated['country'])
                            ->where('province', $validated['province'])
                            ->first();

        if ($location) {
            return response()->json([
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
            ], 200);
        }

        return response()->json(['message' => 'Location not found'], 404);
    }
    
}
