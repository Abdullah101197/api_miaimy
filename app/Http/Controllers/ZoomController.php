<?php

namespace App\Http\Controllers;

use App\Mail\ZoomMeetingInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use MacsiDigital\Zoom\Facades\Zoom;
use App\Services\ZoomMeetingService;

class ZoomController extends Controller
{
    protected $ZoomMeetingService;

    public function __construct(ZoomMeetingService  $ZoomMeetingService)
    {
        $this->ZoomMeetingService = $ZoomMeetingService;
    }

    public function createZoomMeeting($appointment_id)
    {

        
        $responce = $this->ZoomMeetingService->ZoomMeeting($appointment_id);
        return $responce;
    }
    public function lanchZoomMeeting($appointment_id)
    {

        $responce = $this->ZoomMeetingService->lanchZoomMeeting($appointment_id);


        return $responce;
    }
}
