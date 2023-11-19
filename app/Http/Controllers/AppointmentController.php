<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppointmentService;
use App\Http\Requests\AppointmentRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class AppointmentController extends Controller
{


    protected $AppointmentService;

    public function __construct(AppointmentService  $AppointmentService)
    {
        $this->AppointmentService = $AppointmentService;
    }
    public function requestAppointment(AppointmentRequest $request)
    {
        $appontdeatils = $request->validated();
        try {
            $this->AppointmentService->appointmentRequest($appontdeatils);
            return response()->json(['message' => 'Appointment Request sent successfully'], 200);
        } catch (\Exception $e) {
            // Handle exception
            return response()->json(['message' => 'Failed to send Appointment Request'], 500);
        }
    }


    public function getTodayAppointments()
    {
        $result = $this->AppointmentService->getTodayAppointments();

        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);    }
    function viewAppointment()
    {
        $result = $this->AppointmentService->viewAppointment();
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }


    function cancelAppointment($id)
    {
        $result = $this->AppointmentService->cancelAppointment($id);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode($statusCode);
    }
}
