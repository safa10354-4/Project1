<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Hotel;
use App\Models\Rest;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Favcontroller extends Controller
{
    public function addFavorite(Request $request)
    {
        $user = Auth::id();
        $type = $request->input('type');
        $id = $request->input('id');


        switch ($type) {
            case 'hotel':
                $model = Hotel::find($id);
                break;
            case 'restaurant':
                $model = Rest::find($id);
                break;
            case 'trip':
                $model = Trip::find($id);
                break;
            default:
                return response()->json(['message' => 'Invalid type'], 400);
        }

        if (!$model) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Check if the item is already in favorites
        $existingFavorite = Favorite::where('user_id', $user)
            ->where('favoritable_id', $model->id)
            ->where('favoritable_type', get_class($model))
            ->first();


        if ($existingFavorite) {
            return response()->json(['message' => 'Item already in favorites'], 400);
//            $existingFavorite->delete();
//            $existingFavorite->delete();
        }

        // Add to favorites
        $favorite = new Favorite();
        $favorite->user()->associate($user);
        $model->favorites()->save($favorite);
        $model->key = 1; // تأكد من أن لديك عمود key في الجدول
        $model->save();

       return response()->json(['message' => 'Item added to favorites'], 200);
    }

    public function removeFavorite(Request $request)
    {
        $user = Auth::id();
        $type = $request->input('type'); // hotel, restaurant, trip
        $id = $request->input('id');

        switch ($type) {
            case 'hotel':
                $model = Hotel::find($id);
                break;
            case 'restaurant':
                $model = Rest::find($id);
                break;
            case 'trip':
                $model = Trip::find($id);
                break;
            default:
                return response()->json(['message' => 'Invalid type'], 400);
        }

        if (!$model) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $favorite = $model->favorites()->where('user_id', $user)->first();

        if (!$favorite) {
            return response()->json(['message' => 'Item not in favorites'], 400);
        }

        $favorite->delete();
        $model->key =0; // تأكد من أن لديك عمود key في الجدول
        $model->save();

        return response()->json(['message' => 'Item removed from favorites'], 200);
    }


        public function getFavorites()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $favorites = $user->favorites()->with('favoritable')->get();



        if ($favorites->isEmpty()) {
            return response()->json(['message' => 'No favorites found'], 404);
        }

        $items = $favorites->map(function ($favorite) {
            return $favorite->favoritable;
        });

        return response()->json($items);
    }}




