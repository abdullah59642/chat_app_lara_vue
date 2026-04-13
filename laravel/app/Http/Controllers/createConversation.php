<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class createConversation extends Controller
{
    //receiverId is the id of the user you started a conversation with! the other user
    public function createConversation(Request $request)
    {
        $user = $request->user();
        $receiverId = $request->input('receiver_id');
        $checkConversation = Conversation::where('receiver_id',  $user->id)->where('sender_id', $receiverId)->orWhere('receiver_id', $receiverId)->where('sender_id', $user->id)->get();
        if(count($checkConversation) == 0){
            $createdConversation = Conversation::create([
                    'receiver_id' => $receiverId , 
                    'sender_id' => $user->id, 
                    'last_message' => 'Click to start chat',
                    'sender_read_at' => Carbon::now(),
                ]);
            $createMessage = Message::create(['conversation_id'=> $createdConversation->id, 'sender_id' => $user->id]);
            $createdConversation->last_time_message = $createMessage->created_at;
            $createdConversation->save();
            return response()->json(['SUCCESS', 'CONVERSATION CREATED'], 201);
        } elseif(count($checkConversation) >= 1) {
            return response()->json(['MESSAGE', 'CONVERSATION EXISTS'], 402);
        }
    }

    public function getConversations(Request $request)
    {
        $user = $request->user();
        $conversations = Conversation::with('receiverInverseRelation','senderInverseRelation')->where('receiver_id',  $user->id)->orWhere('sender_id', $user->id)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'chats' => $conversations,
        ], 200);
    }

    public function markConversationAsRead(Request $request)
    {   
        $conversation = Conversation::find($request->get('conversation_id'));
        if($conversation->sender_id == $request->get('userId')){
            $conversation->update([
                'sender_read_at' => Carbon::now(),
            ]);
        } else {
            $conversation->update([
                'receiver_read_at' => Carbon::now(),
            ]);
        }
    }

    public function searchUser(Request $request)
    {
        try{
            $query = $request->input('query');
            $users = User::where('name', 'like', '%' . $query . '%')->get();
            return response()->json(['users' => $users], 200);
         } catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
         }
    }

    public function sendMessage(Request $request)
    {
        $user = $request->user();
        $conversationId = $request->input('conversation_id');
        $message = $request->input('body');

        $createdMessage = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $user->id,
            'body' => $message,
        ]);
        $conversation = Conversation::find($conversationId);
        if($conversation->sender_id == $request->get('userId')){
            $conversation->update([
                'last_time_message' => $createdMessage->created_at,
                'sender_read_at' => Carbon::now(),
            ]);
        } else {
            $conversation->update([
                'last_time_message' => $createdMessage->created_at,
                'receiver_read_at' => Carbon::now(),
            ]);
        }
    
        $countWords = strlen($message);
        if($countWords <= 52){
            $conversation->update(['last_message' => $message]);
        }
        elseif($countWords > 52){
            $cuttedMessage = Str::limit($message, 35);
            $conversation->update(['last_message' => $cuttedMessage]);
        }
            broadcast(new MessageSent($conversation, $message))->toOthers();      
        return response()->json(['success' => 'Message sent!'], 201);
    }

    public function showChat(Request $request, $id)
    {
        $user = $request->user();
         $messages = Message::where('conversation_id', $id)
                       ->whereNotNull('body') // Exclude messages where body is null
                       ->orderBy('created_at', 'asc') // Order messages by creation time
                       ->get();
        return response()->json(['messages' => $messages], 200);
    }


     public function getAuthUserId(Request $request)
     {
        try{
            $user = $request->user();
            $userId = $user->id;
            return response()->json(['id' => $userId], 200);
        } catch(Exception $e){
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
     }

    public function showAccountData(Request $request)
    {
        $user = $request->user();
        $userID = $user->id;
        $user = User::findOrFail($userID);
        $userName = $user->name;
        $userEmail = $user->email;
        $userImage = $user->image;
        $image = $userImage;
         return response()->json([
        'id' => $userID,
        'name' => $userName,
        'email' => $userEmail,
        'image' => $image,
         ], 200);
    }

    public function changePicture(Request $request)
    {
        $user = $request->user();
        $userID = $user->id;        
        $user = User::findOrFail($userID);
        $path = $request->file('image')->store('profile_picture', 'public');
        $user->image = asset('storage/'. $path);
        $user->save();
        return response()->json(['success' => 'Updated Successfully'], 201);
    }


     public function getConversationData(Request $request)
     {
      $conversationId =  $request->input('conversation_id');
      $conversation = Conversation::with('senderInverseRelation', 'receiverInverseRelation')->findOrFail($conversationId);
        return response()->json(['data' => $conversation], 200);
     }
}
