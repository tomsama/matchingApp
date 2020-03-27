<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChatRoom;
use App\ChatRoomUser;
use App\ChatMessage;
use App\User;

use App\Events\ChatPusher;

use Auth;

class ChatController extends Controller
{
    public static function show(Request $request){

        $matching_user_id = $request->user_id;


        $current_user_chat_rooms = ChatRoomUser::where('user_id', Auth::id())->pluck('chat_room_id');


        $chat_room_id = ChatRoomUser::whereIn('chat_room_id', $current_user_chat_rooms)
            ->where('user_id', $matching_user_id)
            ->pluck('chat_room_id');


        if ($chat_room_id->isEmpty()){

            ChatRoom::create();

            $latest_chat_room = ChatRoom::orderBy('created_at', 'desc')->first();

            $chat_room_id = $latest_chat_room->id;


            ChatRoomUser::create(
                ['chat_room_id' => $chat_room_id,
                'user_id' => Auth::id()]);

            ChatRoomUser::create(
                ['chat_room_id' => $chat_room_id,
                'user_id' => $matching_user_id]);
        }


        if(is_object($chat_room_id)){
            $chat_room_id = $chat_room_id->first();
        }


        $chat_room_user = User::findOrFail($matching_user_id);

        $chat_room_user_name = $chat_room_user->name;

        $chat_messages = ChatMessage::where('chat_room_id', $chat_room_id)
        ->orderby('created_at')
        ->get();

        return view('chat.show',
        compact('chat_room_id', 'chat_room_user',
        'chat_messages', 'chat_room_user_name'));
    }


    public static function chat(Request $request){

        $chat = new ChatMessage();
        $chat->chat_room_id = $request->chat_room_id;
        $chat->user_id = $request->user_id;
        $chat->message = $request->message;
        $chat->save();

        event(new ChatPusher($chat));
    }
}
