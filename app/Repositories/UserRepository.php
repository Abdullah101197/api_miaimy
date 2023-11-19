<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function createUser(array $userData)
    {
        $otp = mt_rand(100000, 999999);
        
        $user = new User();
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = Hash::make($userData['password']);
        $user->email_verified_at = null; // Mark email as not verified initially
        $user->otp = $otp;
        $user->save();

        return $user;
    }


    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

  public function updateOTP(User $user, int $otp): void
{
    User::where('id', $user->id)->update(['otp' => $otp]);
}

    
    public function getUserById($userId)
    {
        return User::find($userId);
    }
}