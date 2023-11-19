<?php

namespace App\Repositories;

use App\Models\UserDocument;

final class UserDocRepository
{

    // function getUserDoc($user_id)
    // {
    //     $userData = UserDocument::where('user_id', $user_id)
    //         ->latest('updated_at')
    //         ->groupBy('doc_name')
    //         ->get();
    // }

    function getUserDoc($user_id)
    {
        $userData = UserDocument::where('user_id', $user_id)
            ->whereIn('id', function ($query) use ($user_id) {
                $query->selectRaw('MAX(id)')
                    ->from('user_documents')
                    ->where('user_id', $user_id)
                    ->groupBy('doc_name');
            })
            ->get();

        return $userData;
    }

    public function deleteDoc($doc_id)
    {
        $userDocument = UserDocument::where('doc_id', $doc_id)->first();

        if (!$userDocument) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        $userDocument->delete();
        return response()->json(['message' => 'Document soft deleted successfully'], 200);
    }
}
