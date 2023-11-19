<?php

namespace App\Repositories;

use App\Models\AdmissionApplication;
use App\Models\Appointment;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\UserInfo;

final class AdminRepository
{

    function userdetails($user_id)
    {
        $user = User::where('id', $user_id)->get();
        $appoint = Appointment::where('user_id', $user_id)->get();
        $userInfo = UserInfo::where('user_id', $user_id)->get();
        $application = AdmissionApplication::where('user_id', $user_id)->get();

        $userdoc = UserDocument::where('user_id', $user_id)
            ->whereIn('id', function ($query) use ($user_id) {
                $query->selectRaw('MAX(id)')
                    ->from('user_documents')
                    ->where('user_id', $user_id)
                    ->groupBy('doc_name');
            })
            ->get();
        return [
            'user' => $user,
            'userInfo' => $userInfo,
            'userDoc' => $userdoc,
            'appointments' => $appoint,
            'application' => $application, 
        ];
    }
    function appointments($filters)
    {
        $perPage = 10;
        $query = Appointment::query()->with('user');

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('id', 'LIKE', "%$searchTerm%")
                    ->orWhere('name', 'LIKE', "%$searchTerm%")
                    ->orWhere('date', 'LIKE', "%$searchTerm%")
                    ->orWhere('email', 'LIKE', "%$searchTerm%");
            });
        }

        $data = $query->latest()->paginate($perPage);

        return $data;
    }

    function allRetrieveAppointments()
    {
        $allAppointments = Appointment::latest()
            ->with('user')
            ->get(['id', 'agenda', 'date', 'time']);
        return $allAppointments;
    }
    function singleRetrieveAppointments($id)
    {
        $allAppointments = Appointment::where('id', $id)->latest()
            ->with('user')
            ->get();
        return $allAppointments;
    }
    // not in use now
    function searchappointment($searchField) 
    {
        $query = Appointment::query();

        if (isset($searchField['id'])) {
            $query->whereHas('user', function ($q) use ($searchField) {
                $q->where('id', 'like', '%' . $searchField['id'] . '%');
            });
        }

        if (isset($searchField['name'])) {
            $query->whereHas('user', function ($q) use ($searchField) {
                $q->where('name', 'like', '%' . $searchField['name'] . '%');
            });
        }

        if (isset($searchField['email'])) {
            $query->whereHas('user', function ($q) use ($searchField) {
                $q->where('email', 'like', '%' . $searchField['email'] . '%');
            });
        }
        $latestAppointments = array();
        $latestAppointments['data'] = $query->latest()
            ->with('user')
            ->get();

        return $latestAppointments;
    }


    function searchstudent($searchField)
    {
        $students = array();
        $students['data'] = User::where(function ($query) use ($searchField) {
            if (isset($searchField['id'])) {
                $query->where('id', $searchField['id']);
            }
            if (isset($searchField['name'])) {
                $query->orWhere('name', 'like', '%' . $searchField['name'] . '%');
            }

            if (isset($searchField['email'])) {
                $query->orWhere('email', 'like', '%' . $searchField['email'] . '%');
            }
        })
            ->get();
        return $students;
    }
    public function deleteDoc($doc_id)
    {
        $userDocuments = UserDocument::where('doc_id', $doc_id)->delete();

        if ($userDocuments === 0) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        return response()->json(['message' => 'Document deleted successfully'], 200);
    }
}
