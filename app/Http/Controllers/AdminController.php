<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Services\AppointmentService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    protected $AdminService;

    public function __construct(AdminService  $AdminService)
    {
        $this->AdminService = $AdminService;
    }

    function retrieveStudentlist(Request $request)
    {
        $filters = $request->all();
        $result = $this->AdminService->retrieveStudentlist($filters);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    function allretrieveStudentlist()
    {
        $result = $this->AdminService->allretrieveStudentlist();
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }


    function retrieveStudentdetails($id)
    {
        $result = $this->AdminService->retrieveStudentdata($id);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    function searchStudents(Request $request)
    {
        $searchFiled = $request->all();
        $result = $this->AdminService->searchStudents($searchFiled);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    function retrieveAppointments(Request $request )
    {
       $filters = $request->all();
        $result = $this->AdminService->retrieveAppointments($filters);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    function  allRetrieveAppointments()
    {
        $result = $this->AdminService->allRetrieveAppointments();
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    function  singleRetrieveAppointments($id)
    {
        $result = $this->AdminService->singleRetrieveAppointment($id);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }

    function searchappointment(Request $request)
    {
        $searchfiled = $request->all();
        $result = $this->AdminService->searchappointment($searchfiled);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    function createAppointment(AppointmentRequest $request)
    {
        $appontdeatils = $request->validated();
        $id = $request->user_id;

        try {
            $this->AdminService->createAppointment($appontdeatils, $id);
            return response()->json(['message' => 'Appointment Created   successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to Create Appointment'], 500);
        }
    }
    function editAppointment($appointment_id)
    {
        try {
            $data =  $this->AdminService->editAppointment($appointment_id);
            return $data;
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to edit Appointment'], 500);
        }
    }
    function updateAppointment(AppointmentRequest $request, $appointment_id)
    {
        $appontdeatils = $request->validated();
        try {
            $updateAppoint =  $this->AdminService->updateAppointment($appontdeatils, $appointment_id);
            return response()->json(['message' => 'Appointment update   successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update Appointment'], 500);
        }
    }

    function cancelAppointment($id)
    {
        $result = $this->AdminService->cancelAppointment($id);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode($statusCode);
    }
    function comfirmAppointment($appointment_id)
    {
        $result = $this->AdminService->comfirmAppointment($appointment_id);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode($statusCode);
    }
    function progressBar()
    {
        $progressBar = $this->AdminService->progressBar();
        $statusCode = Response::HTTP_OK;
        return response()->json($progressBar, $statusCode)->setStatusCode($statusCode);
    }
}
