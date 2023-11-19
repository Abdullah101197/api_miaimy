<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\UserInfo;

final class AdmissionApplicationRepository
{

    function Notification($applicantID, $missingDocuments)
    {
        Notification::create([
            'user_id' => $applicantID,
            'message' => 'Missing documents: ' . implode(', ', $missingDocuments),
        ]);
    }

    function appointments()
    {
        $latestAppointments = array();
        $latestAppointments = Appointment::latest()
            ->with('user')
            ->paginate(10); // Specify the number of results per page (e.g., 10)
        return  $latestAppointments;
    }
    function allRetrieveAppointments()
    {
        $allAppointments = Appointment::latest()
            ->with('user')
            ->get(['agenda', 'date']);
        return $allAppointments;
    }
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
