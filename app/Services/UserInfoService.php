<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserInfo;
use App\Repositories\UserInfoRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


final class UserInfoService
{

    protected $UserInfoRepository;


    public function __construct(UserInfoRepository $userInfoRepository)
    {
        $this->UserInfoRepository = $userInfoRepository;
    }


    public function storeOrUpdateInfo($userData)
    {
        $user = Auth::user();
        if (isset($userData['profile_picture']) && $userData['profile_picture'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $userData['profile_picture'];
            $fileType = $file->getClientOriginalExtension(); // Get the file type (extension)
            $fileName = time() . '.' . $fileType;
            Storage::disk('public')->put($fileName, file_get_contents($file->getRealPath()));
            $userData['profile_picture'] = $fileName;
        }
        if ($userData['email'] !== null) {
            $user->email = $userData['email'];
        }
        if ($userData['name'] !== null) {
            $user->name = $userData['name'];
        }
        $user->save();
        $status = $user->userInfo()->updateOrCreate(['user_id' => $user->id], $userData);
        return $status;
    }

    public function adminStoreOrUpdateInfo($userData, $user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if (isset($userData['profile_picture']) && $userData['profile_picture'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $userData['profile_picture'];
            $fileType = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $fileType;
            Storage::disk('public')->put($fileName, file_get_contents($file->getRealPath()));
            $userData['profile_picture'] = $fileName;
        }
        if (isset($userData['email'])) {
            $user->email = $userData['email'];
        }
        if (isset($userData['name'])) {
            $user->name = $userData['name'];
        }
        $user->save();
        $status = $user->userInfo()->updateOrCreate(['user_id' => $user_id], $userData);

        return $status;
    }


    public function notication()
    {
        $user_id = auth()->user()->id;

        $unreadNotifications = Notification::where('user_id', $user_id)
            ->where('is_read', false)
            ->get('message');

        $unreadCount = $unreadNotifications->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'unread_messages' => $unreadNotifications,
        ]);
    }
    public function readNotifaction()
    {
        $user_id = auth()->user()->id;
    
        // Mark notifications as read and delete those with is_read = true
        Notification::where('user_id', $user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    
        Notification::where('user_id', $user_id)
            ->where('is_read', true)
            ->delete();
    
        return true;
    }
    
    
    public function retrieveData()
    {
        $user = Auth::user();
        $user_id = $user['id'];
        $Result = $this->UserInfoRepository->getUserData($user_id);
        return $Result;
    }
    public function removeImageData()
    {
        $user = Auth::user();
        $user_id = $user['id'];
        $Result = $this->UserInfoRepository->RemoveImage($user_id);
        return $Result;
    }
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user_id = $user->id;

        $result = $this->UserInfoRepository->deleteUser($user_id);

        if ($result) {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'User deletion failed'], 500);
        }
    }
}
