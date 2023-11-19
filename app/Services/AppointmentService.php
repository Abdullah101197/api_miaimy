<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\Appointment;
use App\Models\UserDocument;
use App\Repositories\AppointmentRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



final class AppointmentService
{

    protected $AppointmentRepository;


    public function __construct(AppointmentRepository $AppointmentRepository)
    {
        $this->AppointmentRepository = $AppointmentRepository;
    }


    public function appointmentRequest($appontdeatils)
    {
        $user = Auth::user();
        $userAppoint = $user->userApoint()->create(['user_id' => $user->id] + $appontdeatils);
        return $userAppoint;
    }

    public function viewAppointment()
    {
        $user = Auth::user();
        $user_id = $user['id'];
        $Result = $this->AppointmentRepository->viewAppointment($user_id);
        return $Result;
    }


    public function cancelAppointment($id)
    {
        $user = Auth::user();
        $status = $this->AppointmentRepository->cancelAppiont($id);
        return $status;
    }
    public function getTodayAppointments()
    {
        $user = Auth::user();
        $user_id = $user->id;
        $today = Carbon::now()->toDateString();
        
        $appointments = Appointment::where('user_id', $user_id)
            ->whereDate('date', $today)
            ->get();
    
        if ($appointments->isEmpty()) {
            return response()->json(['message' => 'No appointments found for today.'], 404);
        }
    
        return response()->json(['appointments' => $appointments]);
    }
}
