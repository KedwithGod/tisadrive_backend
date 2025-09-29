<?php

namespace App\Http\Controllers\Api\V1\Request;

use Carbon\Carbon;
use App\Models\Request\Chat;
use App\Models\User; 
use App\Models\ChatMessage; 
use App\Models\Chat as AdminChat;
use App\Base\Constants\Auth\Role;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Request\Request as RequestModel;
use App\Base\Constants\Masters\PushEnums;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Jobs\NotifyViaMqtt;
use Illuminate\Http\Request;
use App\Jobs\Notifications\SendPushNotification;
use Illuminate\Support\Facades\Validator; 
use Kreait\Firebase\Contract\Database;
use App\Models\Admin\ServiceLocation;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use DB;

/**
 * @group Request-Chat
 * @authenticated
 *
 * APIs for In app chat b/w user/driver
 */
class ChatController extends BaseController
{

    protected $chat;

    protected $database;


    function __construct(Chat $chat,Database $database)
    {
        $this->chat = $chat;
        $this->database = $database;
    }


    /**
     * Chat history for both user & driver
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 
     * {
     *      "success": true,
     *      "message": "chats_listed",
     *      "data": [
     *          {
     *              "id": "dab8a4a2-f904-43ee-9dd2-35ed11321e30",
     *              "message": "hi",
     *              "from_type": 1,
     *              "request_id": "30748675-21bf-478d-b74b-5ba631088138",
     *              "user_id": 29,
     *              "delivered": 0,
     *              "seen": 1,
     *              "created_at": "2024-11-06T14:25:03.000000Z",
     *              "updated_at": "2024-11-06T14:25:48.000000Z",
     *              "message_status": "receive",
     *              "converted_created_at": "7:55 PM"
     *          }
     *      ]
     * }
     */
    public function history(RequestModel $request)
    {

        // Log::info("chat_id");

        $chats = $request->requestChat()->orderBy('created_at', 'asc')->get();

        if (access()->hasRole(Role::USER)) {
            $from_type = 1;
        } else {
            $from_type = 2;
        }
        foreach ($chats as $key => $chat) {
            if ($chat->from_type == $from_type) {

                $chats[$key]['message_status'] = 'send';
            } else {
                $chats[$key]['message_status'] = 'receive';
            }
        }

        return $this->respondSuccess($chats, 'chats_listed');
    }

    /**
     * Update Seen
     * 
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "message_seen_successfully",
     * }
     * */
    public function updateSeen(Request $request){

        if (access()->hasRole(Role::USER)) {
            $seen_from_type = 2;
        } else {
            $seen_from_type = 1;
        }

        $request_detail = RequestModel::find($request->request_id);

        // $request_detail->requestChat()->where('from_type',$seen_from_type)->update(['seen'=>true]);

        if($request_detail && $request_detail->requestChat()) // Ensure $request_detail is not null and requestChat() is not null
        {
            $request_detail->requestChat()->where('from_type', $seen_from_type)->update(['seen' => true]);
        } 
        // else {
        //     return $this->respondError('Request or chat not found', 404);
        // }


        return $this->respondSuccess(null, 'message_seen_successfully');


    }

    /**
     * Send Chat Message
     * @bodyParam request_id uuid required request id of the trip
     * @bodyParam message string required message of chat
     * @response
     * {
     *     "success": true,
     *     "message": "message_sent_successfully",
    
     * }
     */
    public function send(Request $request)
    {
        if (access()->hasRole(Role::USER)) {
            $from_type = 1;
        } else {
            $from_type = 2;
        }

        $request_detail = RequestModel::find($request->request_id);

        $request_detail->requestChat()->create([
            'message' => $request->message,
            'from_type' => $from_type,
            'user_id' => auth()->user()->id
        ]);

        $chats = $request_detail->requestChat()->orderBy('created_at', 'asc')->get();


        if (access()->hasRole(Role::USER)) {
            $from_type = 1;
            $user_type = 'user';
            $driver = $request_detail->driverDetail;
            $notifable_driver = $driver->user;
        } else {
            $from_type = 2;
            $user_type = 'driver';
            $driver = $request_detail->userDetail;
            $notifable_driver = $driver;
        }
        foreach ($chats as $key => $chat) {
            if ($chat->from_type == $from_type) {

                $chats[$key]['message_status'] = 'receive';
            } else {
                $chats[$key]['message_status'] = 'send';


            }
        }


        // $socket_data = new \stdClass();
        // $socket_data->success = true;
        // $socket_data->success_message  = PushEnums::NEW_MESSAGE;
        // $socket_data->data = $chats;

        // dispatch(new NotifyViaMqtt('new_message_' . $driver->id, json_encode($socket_data), $driver->id));


        // $title = custom_trans('new_message_from',[],auth()->user()->lang)." " . auth()->user()->name;
        // $body = $request->message;

        // dispatch(new SendPushNotification($notifable_driver,$title,$body));


        $notification = \DB::table('notification_channels')
            ->where('topics', 'New Chat Message') // Match the correct topic
            ->first();
            
            // send push notification 
            if ($notification && $notification->push_notification == 1) {
                 // Determine the user's language or default to 'en'
                $userLang = auth()->user()->lang ?? 'en';
                // dd($userLang);

                // Fetch the translation based on user language or fall back to 'en'
                $translation = \DB::table('notification_channels_translations')
                    ->where('notification_channel_id', $notification->id)
                    ->where('locale', $userLang)
                    ->first();

                // If no translation exists, fetch the default language (English)
                if (!$translation) {
                    $translation = \DB::table('notification_channels_translations')
                        ->where('notification_channel_id', $notification->id)
                        ->where('locale', 'en')
                        ->first();
                }
        
                
                $title =  $translation->push_title ?? $notification->push_title;
                $body = strip_tags($translation->push_body ?? $notification->push_body);
                dispatch(new SendPushNotification($notifable_driver,$title,$body));
            }

        return $this->respondSuccess(null, 'message_sent_successfully');
    }


//conversation Starts Here
    /**
     * Initiate Conversation
     * @response
     * {
     *     "success": true,
     *     "data": {
     *         "conversation": [
     *             {
     *                 "id": "6c96f90a-bb44-4c8a-8542-e1c93f488085",
     *                 "conversation_id": "bb81d8bd-5138-4726-83f4-4903393c861a",
     *                 "sender_id": "9",
     *                 "unseen_count": 0,
     *                 "sender_type": "user",
     *                 "content": "Vanakam bha",
     *                 "created_at": "2024-10-28T18:09:39.000000Z",
     *                 "updated_at": "2024-10-29T11:24:00.000000Z",
     *                 "user_timezone": "28th Oct 11:39 PM",
     *                 "converted_created_at": "28 Oct 2024 03:09 PM"
     *             }
     *         ],
     *         "new_chat": 0,
     *         "conversation_id": "bb81d8bd-5138-4726-83f4-4903393c861a",
     *         "count": 0
     *     }
     * }
     */
    public function initiateConversation(Request $request)
    {   
        $user_id = auth()->user()->id;   
        $country = auth()->user()->country;
        $timezone = ServiceLocation::where('country',$country)->pluck('timezone')->first()?:'UTC'; 
        $check_data_exists =  Conversation::where('user_id', $user_id)->where('is_closed', false)->first();
        if($check_data_exists)
        {
            $messages = Message::where('conversation_id',$check_data_exists->id)->orderBy('created_at','ASC')->get();
            foreach($messages as $k=>$v)
            {
                $v->user_timezone = Carbon::parse($v->created_at)->setTimezone($timezone)->format('jS M h:i A'); 
            }
            $data['conversation'] = $messages;
            $data['new_chat'] = 0;
            $data['conversation_id'] = $check_data_exists->id; 
            $data['count'] = 0;  
            $response_array = array("success"=>true,'data'=>$data);
        }
        else{ 
            $data['conversation'] = [];
            $data['new_conversation'] = 1;  
            $response_array = array("success"=>true,'data'=>$data);
        }
        return response()->json($response_array);  
    }

   /**
     * send message to admin
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 
     * {
     *     "success": true,
     *     "data": {
     *         "message": "Hello",
     *         "message_id": "bdfd2cf0-331e-4e4c-a607-da3701eb21aa",
     *         "conversation_id": "bb81d8bd-5138-4726-83f4-4903393c861a",
     *         "sender_type": "user",
     *         "sender_id": 9,
     *         "created_at": {
     *             ".sv": "timestamp"
     *         },
     *         "message_success": "Message Sended successfully",
     *         "user_timezone": "25th Nov 07:00 PM"
     *     }
     * }
     */
    public function sendMessage(Request $request)
    {
        $validate_array = [
            'new_conversation' => 'required', 
            'content' => 'required',
           ];
        $validator = Validator::make($request->all(),$validate_array );
        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_array = array("success"=>false,"message"=>$errors->all());
            return response()->json($response_array); 
        }   
        if($request->new_conversation == 1)
        {
            $check_data_exists =  Conversation::where('user_id', auth()->user()->id)->where('is_closed', false)->first();

            if(!$check_data_exists)
            {
                $conversation = new Conversation(); 
                $conversation->user_id = auth()->user()->id;
                $conversation->subject = auth()->user()->name;
                $conversation->save();  
                $conversation_id = $conversation->id;
            }else{
                $conversation_id = $check_data_exists->id;

            }

        } 
        else{
            $conversation_id = $request->conversation_id;
        }
        $messages = new Message();
        $messages->conversation_id = $conversation_id;
        $messages->sender_id = auth()->user()->id;
        $messages->sender_type = 'user';
        $messages->content = $request->content;
        $messages->unseen_count = true;
        $messages->save();  
        $data = [
            'message' => $request->content, 
            'message_id' => $messages->id, 
            'conversation_id' => $conversation_id, 
            'sender_type' => 'user', 
            'sender_id' => auth()->user()->id, 
            'created_at'=> Database::SERVER_TIMESTAMP
        ]; 
        $chatRef = $this->database->getReference('conversation/'.$conversation_id);
       
        $NewchatRef = $chatRef->set($data);
       
        $conversation_id = $NewchatRef->getKey(); 
        
        $data['message_success'] = "Message Sended successfully"; 
     
        $country = auth()->user()->country;
      
        $timezone = ServiceLocation::where('country',$country)->pluck('timezone')->first()?:'UTC'; 
      
        $data['user_timezone'] = Carbon::parse($messages->created_at)->setTimezone($timezone)->format('jS M h:i A'); 
       
       
        return response()->json(["success"=>true,'data' => $data]); 
    }  


}
  