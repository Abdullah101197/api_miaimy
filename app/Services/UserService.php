<?php

namespace App\Services;

use App\Jobs\SendVerificationEmailJob;
use App\Mail\ResetEmail;
use App\Mail\VerificationEmail;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Notifications\VerificationEmailNotification;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;


class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $userData)
    {
        $user = $this->userRepository->createUser($userData);
        SendVerificationEmailJob::dispatch($user);
        return $user;
    }



    public function markEmailAsVerified(User $user)
    {
        $user->markEmailAsVerified();
    }

    public function adminLogin(array $credentials)
    {

        if (!Auth::guard('admin')->attempt($credentials)) {
            return false;
        }

        $user = Auth::guard('admin')->user();
        $token = $user->createToken('adminAuthToken')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => 'admin',

        ];
    }
    public function userLogin(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return false;
        }

        $user = Auth::user();

        $token = $user->createToken('authToken')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => 'user',

        ];
    }

    public function sendPasswordResetLink(array $credentials)
    {
        return Password::sendResetLink($credentials);
    }

    public function resetUserPassword(array $passwordResetData, $callback)
    {
        return Password::reset($passwordResetData, $callback);
    }

    public function getUserById($userId)
    {
        return $this->userRepository->getUserById($userId);
    }

    public function resendVerificationEmail($user)
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        SendVerificationEmailJob::dispatch($user);
        return $user;
    }

    public function forgotPassword(string $email)
    {
        try {
            $user = $this->userRepository->getUserByEmail($email);

            if (!$user) {
                return ['message' => 'Invalid email address'];
            }

            $otp = rand(100000, 999999); // Generate a random 6-digit OTP

            $this->userRepository->updateOTP($user, $otp);

            \Mail::to($user->email)->send(new ResetEmail($user, $otp));

            return ['message' => 'OTP sent to your email address'];
        } catch (\Exception $e) {
            return ['message' => 'An error occurred while sending the OTP. Error: ' . $e->getMessage()];
        }
    }

    public function resetPassword(array $requestData)
    {
        try {
            $user = User::where('email', $requestData['email'])->first();

            if ($user && $user->otp === $requestData['otp']) {
                $user->forceFill([
                    'password' => Hash::make($requestData['password']),
                ])->save();

                event(new PasswordReset($user));

                return ['message' => 'Password reset successful'];
            } else {
                return ['message' => 'Unable to reset password'];
            }
        } catch (\Exception $e) {
            return ['message' => 'An error occurred while resetting the password. Error: ' . $e->getMessage()];
        }
    }



    function changePassword($password)
    {
        $old_password = $password['old_password'];
        $new_password = $password['new_password'];
        $user = auth()->user();
        if (Hash::check($old_password, $user->password)) {
            $new_password_hash = Hash::make($new_password);
            $user->password = $new_password_hash;
            $user->save();
            return 'Password changed successfully.';
        }

        return 'Invalid old password.';
    }
}
