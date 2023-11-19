<?php

namespace App\Services;

use Pusher\Pusher;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class ChatService
{


    public function sendMessage($message, $receiverId)
    {
        $user = Auth::user();


        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }


        if ($user->isAdmin) {
            // Admin is sending the message
            $senderId = $user->id;
            $receiverId = $receiverId; // Assuming you send receiver_id in the request

        } else {
            // Customer is sending the message
            $senderId = $user->id;
            $receiverId = 1; // Assuming you send receiver_id in the request

        }

        $message = ChatMessage::create([
            'content' => $message,
            'user_id' => $senderId,
            'receiver_id' => $receiverId,
        ]);

        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $pusher->trigger('chat', 'new-message', ['message' => $message]);
        // Send a notification to the receiver
        $pusher->trigger('notifications', 'new-chat', [
            'message' => 'You have a new chat message',
            'receiver_id' => $receiverId,
        ]);

        return response()->json(['message' => 'Message sent successfully']);
    }


    public function getMessages()
    {

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $isAdmin = $user->isAdmin;

        $messages = ChatMessage::orderBy('created_at', 'asc')
            ->where(function ($query) use ($user, $isAdmin) {
                if ($isAdmin) {
                    $query->where('receiver_id', 1);
                } else {
                    $query->where('user_id', $user->id)
                        ->orWhere('receiver_id', $user->id);
                }
            })
            ->get();

        return response()->json(['messages' => $messages]);
    }


    public function recentChat($userQuery)
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Retrieve the last fetched user IDs from session or cache
        $lastFetchedUserIds = session('last_fetched_user_ids', []);
    
        $recentChat = ChatMessage::whereIn('receiver_id', function ($query) use ($user) {
            $query->select('id')
                ->from('users')
                ->where('id', '!=', $user->id); // Exclude the current user
        })
        ->where('content', 'LIKE', "%{$userQuery}%")
        ->with(['receiver:id,name', 'user:id,name,email']) // Eager load necessary data
        ->orderBy('created_at', 'desc')
        ->get();
    
        // Extract receiver's name and id from the result
        $formattedChat = $recentChat->map(function ($chatMessage) {
            $unreadCount = ChatMessage::where('receiver_id', $chatMessage->receiver->id)
                ->where('is_read', false) // Consider only unread messages
                ->count();
    
            return [
                'receiver_id' => $chatMessage->receiver->id,
                'receiver_name' => $chatMessage->receiver->name,
                'user_name' => $chatMessage->user->name,
                'user_email' => $chatMessage->user->email,
                'unread_count' => $unreadCount,
            ];
        });
    
        return response()->json(['recent_chat' => $formattedChat]);
    }
    




    public function chatHistory($query, $userQuery)
    {

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $queryBuilder = ChatMessage::whereIn('user_id', [$user->id, $user->id])
            ->orWhereIn('receiver_id', [$user->id, $user->id]);

        if ($query) {
            $queryBuilder->where('content', 'LIKE', "%{$query}%");
        }

        if ($userQuery) {
            $userIds = User::where('name', 'LIKE', "%{$userQuery}%")
                ->pluck('id')
                ->toArray();

            // Filter out messages where the searched user's ID is not present in user_id or receiver_id
            $queryBuilder->where(function ($query) use ($userIds) {
                $query->whereIn('user_id', $userIds)
                    ->orWhereIn('receiver_id', $userIds);
            });
        }
        $recentChat = $queryBuilder->with(['user', 'receiver']) // Eager load user and receiver relationships
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['chat_history' => $recentChat]);
    }

    function chatMessages($chatId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $messages = ChatMessage::where(function ($query) use ($user, $chatId) {
            $query->where(function ($subQuery) use ($user, $chatId) {
                $subQuery->where('user_id', $user->id)
                    ->where('receiver_id', $chatId);
            })
                ->orWhere(function ($subQuery) use ($user, $chatId) {
                    $subQuery->where('user_id', $chatId)
                        ->where('receiver_id', $user->id);
                });
        })
            ->orderBy('created_at', 'asc')
            ->get();
            foreach ($messages as $key => $message) {
                if (!$message->is_read) {
                    ChatMessage::where('id',$message->id)->update(['is_read' => true]);

                }
            }
            

        return response()->json(['chat_messages' => $messages]);
    }
}
