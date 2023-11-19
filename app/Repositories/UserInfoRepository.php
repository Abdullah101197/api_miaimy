<?php

namespace App\Repositories;

use App\Models\AdmissionApplication;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\UserInfo;
use App\Models\ZoomMeeting;

final class UserInfoRepository
{

    function getUserData($user_id)
    {
        $userInfo = UserInfo::where('user_id', $user_id)->get();
        $user = User::where('id', $user_id)->get();
        return [
            'user' => $user,
            'userInfo' => $userInfo
        ];
    }
    function RemoveImage($user_id)
    {
        $UserData = UserInfo::where('user_id', $user_id)->update(['profile_picture' => null]);
        return $UserData;
    }
    function deleteUser($user_id)
    {

        $user = User::withTrashed()->find($user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        UserInfo::where('user_id', $user_id)->delete();
        AdmissionApplication::where('user_id', $user_id)->delete();
        Appointment::where('user_id', $user_id)->delete();
        ZoomMeeting::where('user_id', $user_id)->delete();
        UserDocument::where('user_id', $user_id)->delete();
        Notification::where('user_id', $user_id)->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
