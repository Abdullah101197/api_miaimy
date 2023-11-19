<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserInfo;
use App\Services\UserInfoService;
use App\Http\Requests\UserInfoRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class UserInfoController extends Controller
{


    protected $userInfoService;

    public function __construct(UserInfoService  $userInfoService)
    {
        $this->userInfoService = $userInfoService;
    }

    function storeInfo(UserInfoRequest $request)
    {
        $userData = $request->validated();
        $this->userInfoService->storeOrUpdateInfo($userData);
        $message = 'User info stored successfully';
        $statusCode = Response::HTTP_CREATED;
        return response()->json(['message' => $message], $statusCode)
            ->setStatusCode(200);
    }

    function adminStoreInfo(UserInfoRequest $request,$user_id)
    {
        $userData = $request->validated();
        $this->userInfoService->adminStoreOrUpdateInfo($userData,$user_id);
        $message = 'User info stored successfully';
        $statusCode = Response::HTTP_CREATED;
        return response()->json(['message' => $message], $statusCode)
            ->setStatusCode(200);
    }

    function notication(Request $request)
    {
       $message = $this->userInfoService->notication();
       return $message;
    }
    
    function readNotifaction(Request $request)
    {
       $message = $this->userInfoService->readNotifaction();
       return $message;
    }
    function retrieveInfo()
    {
        $result = $this->userInfoService->retrieveData();
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    function removeImage()
    {
        $result = $this->userInfoService->removeImageData();
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    function deleteuser($id)
    {

        $result = $this->userInfoService->deleteUser($id);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }

}
