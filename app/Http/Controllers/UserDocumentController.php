<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserInfo;
use App\Services\UserDocService;
use App\Http\Requests\UserDocRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class UserDocumentController extends Controller
{


    protected $UserDocService;

    public function __construct(UserDocService  $UserDocService)
    {
        $this->UserDocService = $UserDocService;
    }

    function uploadDoc(UserDocRequest $request)
    {
        $userDoc = $request->validated();

        try {
            $this->UserDocService->storeDocument($userDoc);
            return response()->json(['message' => 'Document stored successfully'], 200);
        } catch (\Exception $e) {
            // Handle exception
            return response()->json(['message' => 'Failed to store document'], 500);
        }
    }

    function retrieveDoc()
    {
        $result = $this->UserDocService->retrieveDoc();
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }


    function DeleteDoc($id)
    {
        $doc_id = $id;
        $result = $this->UserDocService->delectDoc($doc_id);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode($statusCode);
    }
}
