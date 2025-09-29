<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Message;
use Kreait\Firebase\Contract\Database;
use App\Base\Filters\Admin\UserFilter;
use App\Models\Admin\ServiceLocation;
use App\Base\Libraries\QueryFilter\QueryFilterContract;


class ChatController extends Controller
{
    protected $database;


    function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function index() 
    {
        $firebaseConfig = (object) [
            'apiKey' => get_firebase_settings('firebase_api_key'),
            'authDomain' => get_firebase_settings('firebase_auth_domain'),
            'databaseURL' => get_firebase_settings('firebase_database_url'),
            'projectId' => get_firebase_settings('firebase_project_id'),
            'storageBucket' => get_firebase_settings('firebase_storage_bucket'),
            'messagingSenderId' => get_firebase_settings('firebase_messaging_sender_id'),
            'appId' => get_firebase_settings('firebase_app_id'),
        ];

        // Fetch the conversation data
        $conversations = Conversation::where('is_closed', false)
            ->orderBy('created_at','DESC')
            ->withSum('messages as unread', 'unseen_count')
            ->get();

        foreach ($conversations as $key => $chat) {
            if($chat->messages()->exists()){
                $chat->last_seen = $chat->messages[0]->converted_created_at;
            }
        }
        $conversationId = request()->conversation_id;
        
        return Inertia::render('pages/chat/index', [
            'firebaseConfig' => $firebaseConfig,
            'selectedConversationId' => $conversationId,
            'conversations' => $conversations, // Pass the conversations to the view
        ]);
    }
    
    public function messages(Conversation $conversationId)
    {
        // Get all messages for the specific conversation, ordered by created_at
        $messages = $conversationId->messages()->orderBy('created_at', 'Asc')->get();
    
        // Transform the messages to the desired structure
        $formattedMessages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'align' => $message->sender_type === 'user' ? 'left' : 'right', // Assuming 'user' messages appear on the right
                'message' => $message->content,
                'profile_picture' => $message->profile_picture,
                'time' => $message->converted_created_at
            ];
        });
        foreach($messages as $key=> $message){
            $message->update(['unseen_count'=>0]);
        }
    
        return response()->json($formattedMessages); // Return JSON response
    }
    
    public function sendAdmin(Request $request)
    {
        $message = Message::create(['conversation_id'=>$request->conversationId,'sender_id'=>auth()->user()->id,
                'sender_type'=>'admin','content'=>$request->message, 'unseen_count'=>false]);


        $chat = [
            'conversation_id'=>$request->conversationId,'sender_id'=>auth()->user()->id,
            'message_id' => $message->id, 
            'created_at'=> Database::SERVER_TIMESTAMP,
            'sender_type'=>'admin','message'=>$request->message,
        ];
        
        $this->database->getReference('conversation/'.$request->conversationId)->set($chat);

        return response()->json([
            'successMessage' => 'message Sended successfully',
        ]);
   }
   public function closeChat(Request $request)
   {

    $conversations = Conversation::where('id', $request->conversationId)->update(['is_closed'=>true]);

        $this->database->getReference('conversation/'.$request->conversationId)->update(['is_closed'=>true]);
        $this->database->getReference('conversation/'.$request->conversationId)->remove();
        // dd($conversations);

        return response()->json([
            'successMessage' => 'Conversation Closed successfully',
        ]);
   }

   public function createChat(Request $request)
   {
        $request->validate(['user_id'=>'required']);
        $user = User::find($request->user_id);
        if(!$user){
            return response()->json([
                'message' => 'Conversation Closed successfully',
            ],403);
        }
        $user_id = $user->id;   
        $country = $user->country;
        $timezone = ServiceLocation::where('country',$country)->pluck('timezone')->first()?:'UTC';
        $check_data_exists =  Conversation::where('user_id', $user_id)->where('is_closed', false)->first();

        if(!$check_data_exists)
        {
            $conversation = new Conversation(); 
            $conversation->user_id = $user->id;
            $conversation->subject = $user->name;
            $conversation->save();
        }else{
            $conversation = $check_data_exists;

        }
        return response()->json($conversation);
   }
   public function fetchUser(Request $request)
   {
        $request->validate(['user_id' => 'required']);
        $user = User::Find($request->user_id);

        return response()->json($user);
   }
   public function fetchChats()
   {
        // Fetch the conversation data
        $conversations = Conversation::where('is_closed', false)->orderBy('created_at','DESC')
            ->withSum('messages as unread', 'unseen_count')
            ->having('unread','>',0)
            ->get();

        foreach ($conversations as $key => $chat) {
            $chat->last_seen = $chat->messages[0]->converted_created_at;
            $chat->last_message = $chat->messages[0]->content;
        }

        return response()->json($conversations);
   }
   public function readAll()
   {
        // Fetch the conversation data
        $conversations = Conversation::where('is_closed', false)
            ->withSum('messages as unread', 'unseen_count')
            ->having('unread','>',0)
            ->get();

        foreach ($conversations as $key => $chat) {
            $unread_messages = $chat->messages()->where('unseen_count',true)->get();
            foreach ($unread_messages as $key => $message) {
                $message->update(['unseen_count' => false]);
            }
        }
        $this->database->getReference('conversation')->update(['readMark'=>true]);
        $this->database->getReference('conversation/readMark')->remove();

        return response()->json([
            'successMessage' => 'Conversation Marked as Read successfully',
        ]);
   }

   public function searchUser(QueryFilterContract $queryFilter, Request $request)
   {
        $existingIds = Conversation::where('is_closed', false)
            ->pluck('user_id')->toArray();
            // dd($existingIds);
       $query = User::orderBy('created_at','DESC')->whereNotIn('id',$existingIds);


       $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();

       return response()->json([
           'data' => $results->items(),
       ]);
   }
   public function verifyChat(Request $request)
   {
        $conversation = Conversation::where('is_closed',false)->where('id',$request->conversationId)->first();
        return response()->json([
            'data' => $conversation,
        ]);
   }
}
