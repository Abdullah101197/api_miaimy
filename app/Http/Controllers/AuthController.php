<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\URL;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use App\Services\UserService;

class AuthController extends Controller
{

    protected $userService;
    protected $AdminService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function register(UserStoreRequest $request)
    {
        $post_data = $request->validated();
        $user = $this->userService->createUser($post_data);
        event(new Registered($user));
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',

        ]);
    }



    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        $isAdmin = $user->isAdmin;
        if ($isAdmin == 1) {
            // Perform admin login
            $token = $this->userService->adminLogin($credentials);
        } else {
            // Perform user login
            $token = $this->userService->userLogin($credentials);
        }

        if (!$token) {
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        return response()->json($token);
    }

    public function verify(Request $request, $id, $otp)
    {
        $user = User::findOrFail($id);

        if (sha1($user->otp) === $otp) {
            $user->markEmailAsVerified();
            return response()->json([
                'message' => 'Email verification successful.',
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid verification link.',
        ], 400);
    }

    public function resend(Request $request)
    {
        $user = $request->user();
        $resendStatus = $this->userService->resendVerificationEmail($user);

        if (!$resendStatus) {
            return response()->json([
                'message' => 'Email has already been verified.',
            ], 400);
        }

        return response()->json([
            'message' => 'Verification email resent.',
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $email = $request->input('email');
        $response = $this->userService->forgotPassword($email);
        return response()->json($response);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $requestData = $request->only('otp', 'email', 'password');
        $response = $this->userService->resetPassword($requestData);

        return response()->json($response);
    }

    function changePassword(Request $request)
    {

        $password = $request->validate([
            'old_password' => '',
            'new_password' => 'required|min:8'
        ]);

        $response = $this->userService->changePassword($password);
        return response()->json($response);



    }
}
