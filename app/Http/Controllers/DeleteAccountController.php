<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeleteAccountController extends Controller
{
    //Soft Delete Records mobile
    public function softDelete($id)
    {
        $post = User::find($id);

        if (!$post) {
            return response()->json(['message' => 'Account not found'], 404);
        }
        $post->delete();
        return response()->json(['message' => 'Account  soft deleted']);
    }

    public function softDeleteweb($id)
    {
        $post = Admin::find($id);

        if (!$post) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Account soft deleted']);
    }
    ////////////////////////////////
    //Showing Records
    public function showPosts()
    {
        $posts = User::get();

        return response()->json(['posts' => $posts]);
    }
    // Show Soft Deleted Records (Optional)
    public function showSoftDeletedPosts()
    {
        $softDeletedPosts = User::onlyTrashed()->get();

        return response()->json(['soft_deleted_posts' => $softDeletedPosts]);
    }
   // Restore Soft Deleted Records
    public function restorePost($id)
    {
        $post = User::withTrashed()->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->restore();

        return response()->json(['message' => 'Post restored']);
    }
    //Permanently Delete Records
    public function forceDeletePost($id)
    {
        $post = User::withTrashed()->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->forceDelete();

        return response()->json(['message' => 'Post permanently deleted']);
    }

}
