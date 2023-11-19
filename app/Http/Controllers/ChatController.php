<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pusher\Pusher;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ChatService;


class ChatController extends Controller
{

    protected $ChatService;

    public function __construct(ChatService  $ChatService)
    {
        $this->ChatService = $ChatService;
    }
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        $receiverId = $request->input('receiver_id');
        $result = $this->ChatService->sendMessage($message, $receiverId);
        return response()->json(['message' => 'Message sent successfully']);
    }

    public function getMessages()
    {
        $result = $this->ChatService->getMessages();
        return $result;
    }
    public function recentChat(Request $request)
    {
        $query = $request->input('query');
        $userQuery = $request->input('user_query');
        $recentChat = $this->ChatService->recentChat($query, $userQuery);
        return $recentChat;
    }


    public function chatHistory(Request $request)
    {

        $query = $request->input('query');
        $userQuery = $request->input('user_query');
        $recentChat = $this->ChatService->chatHistory($query, $userQuery);
        return $recentChat;
    }


    public function chatMessages($chatId)
    {

        $chatMessages = $this->ChatService->chatMessages($chatId);
        return $chatMessages;
    }
}
