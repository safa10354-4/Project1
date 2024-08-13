<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validatedData = $request->validate([
            'admin_id' => 'required',
            'message' => 'required|string',
        ]);
        $sender = Auth::user();
        $senderType = $sender->is_user ? 'admin' : 'user';
        $conversation = Conversation::firstOrCreate([
            'user_id' => $sender->id,
            'admin_id' => $validatedData['admin_id'],
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'message' => $validatedData['message'],
            'sender_type' => $senderType,
//            'senderType' =>  $conversation->user_id ? 'user' : 'admin',
            'sender_name'=>$sender->name,
            'sender_image'=>$sender->image,
        ]);

//        $receiver= $validatedData['admin_id'];
        $receiver = Admin::find($validatedData['admin_id']);
//        event(new \App\Events\Message($message->message, $sender->name, $message->sender_type));
//        event(new \App\Events\Message($message->message, $sender->name, $message->senderType, $sender->id, $receiver->id));

        event(new \App\Events\Message($message->message, $sender->name,$sender->is_user ? 'admin' : 'user' , $sender->id, $receiver->id,$sender->image));
        return response()->json(['message' => 'تم إرسال الرسالة بنجاح', 'data' => $message], 200);
    }

    public function getMessages($adminId)
    {
        $conversation = Conversation::where(function ($query) use ($adminId) {
            $query->where('user_id', Auth::id())
                ->where('admin_id', $adminId);
        })->orWhere(function ($query) use ($adminId) {
            $query->where('admin_id', Auth::id())
                ->where('user_id', $adminId);
        })->first();

        if (!$conversation) {
            return response()->json(['message' => 'No conversation found'], 404);
        }

        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();


//        $messages = $messages->map(function ($message) use ($conversation) {
//            return [
//                'id' => $message->id,
//                'message' => $message->message,
//                'sender_id' => $message->sender_id,
////                'sender_type' => $message->sender_id == $conversation->user_id ? 'user' : 'admin',
//                'sender_type' => $message->sender_type,
//                'created_at' => $message->created_at,
//            ];
//        });

        return response()->json(['messages' => $messages], 200);
    }
    public function sendMessageAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $conversation = Conversation::firstOrCreate([
            'admin_id' => Auth::id(),
            'user_id' => $validatedData['user_id'],
        ]);

        $sender = Auth::user();
        $senderType=  $sender->admin_id ? 'user' : 'admin';
        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $validatedData['message'],
//            'sender_type' => $sender->admin_id ? 'user' : 'admin',
            'sender_type' => $senderType,
            'sender_name'=>$sender->name,
            'sender_image'=>$sender->image,
        ]);

//        $receiver=$validatedData['user_id'];
        $receiver = User::find($validatedData['user_id']);

//        event(new \App\Events\Message($message->message, $sender->name, $message->sender_type));
//        event(new \App\Events\Message($message->message, $sender->name, $message->senderType, $sender->id, $receiver->id));
//        $sender->admin_id ? 'user' : 'admin',
event(new \App\Events\Message($message->message, $sender->name,$sender->admin_id ? 'user' : 'admin' , $sender->id, $receiver->id,$sender->image));

        return response()->json(['message' => 'Message sent successfully', 'data' => $message], 200);
    }


    public function getMessagesAdmin($userId)
    {
        $conversation = Conversation::where(function ($query) use ($userId) {
            $query->where('admin_id', Auth::id())
                ->where('user_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_id', Auth::id())
                ->where('admin_id', $userId);
        })->first();

        if (!$conversation) {
            return response()->json(['message' => 'No conversation found'], 404);
        }

        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();


//        $messages = $messages->map(function ($message) use ($conversation) {
//            return [
//                'id' => $message->id,
//                'message' => $message->message,
//                'sender_id' => $message->sender_id,
////                'sender_type' => $message->sender_id == $conversation->user_id ? 'user' : 'admin',
//                'sender_type' => $message->sender_type,
//                'created_at' => $message->created_at,
//            ];
//        });

        return response()->json(['messages' => $messages], 200);
    }
    public function getAllUsersWithAdmin()
    {

        $conversations = Conversation::where('admin_id', Auth::id())->get();


        $users = $conversations->map(function ($conversation) {
            return $conversation->user;
        });

        return response()->json(['users' => $users], 200);
    }
}
