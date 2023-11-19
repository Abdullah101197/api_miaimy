<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\UserDocument;
use App\Repositories\UserDocRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



final class UserDocService
{

    protected $UserDocRepository;


    public function __construct(UserDocRepository $UserDocRepository)
    {
        $this->UserDocRepository = $UserDocRepository;
    }


    public function storeDocument($userDoc)
    {
        $user = Auth::user();
        if (isset($userDoc['file']) && $userDoc['file'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $userDoc['file'];
            $fileorgName = $file->getClientOriginalName();
            $fileType = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $fileType;
            Storage::disk('public')->put($fileName, file_get_contents($file->getRealPath()));
            $userDoc['file'] = $fileName;
            $userDoc['doc_orignial_name'] = $fileorgName;
            $userDoc['doc_type'] = $fileType;
        }
            $userDoc['doc_id'] = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            $userDocument = $user->userDoc()->create(['user_id' => $user->id] + $userDoc);
            return $userDocument;
    }
    public function retrieveDoc()
    {
        $user = Auth::user();
        $user_id = $user['id'];
        $Result = $this->UserDocRepository->getUserDoc($user_id);
        return $Result;
    }

    public function delectDoc($doc_id)
    {
        $user = Auth::user();
        $status = $this->UserDocRepository->deleteDoc($doc_id);
        return $status;
    }
}
