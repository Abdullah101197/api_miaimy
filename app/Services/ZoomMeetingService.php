<?php

namespace App\Services;

use App\Helpers\ZoomAccesToken;
use App\Models\Appointment;
use App\Models\User;
use App\Repositories\ZoomRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\ZoomMeetingInvitation;
use App\Models\ZoomMeeting;

final class ZoomMeetingService
{
    protected $zoomRepository;

    public function __construct(ZoomRepository $zoomRepository)
    {
        $this->zoomRepository = $zoomRepository;
    }

    public function ZoomMeeting($appointment_id)
    {
        try {
            $accessToken = ZoomAccesToken::getAccessToken();
            $base_url = config('zoom.base_url');
            $endpoint = $base_url . 'users/me/meetings';
            $upcomingAppointments = Appointment::where('id', $appointment_id)
                ->get();
            foreach ($upcomingAppointments as $appointment) {
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $appointment->date . ' ' . $appointment->time);
                $meetingData = [
                    'topic' => $appointment->agenda,
                    'type' => 2,
                    'start_time' => now()->toIso8601String(),
                    'duration' => 60,
                ];

                $response = Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($endpoint, $meetingData);

                $meetingDetails = $response->json();
                $this->zoomRepository->storeZoomMeeting($upcomingAppointments, $meetingDetails);
                $userEmail = User::where('id', $appointment->user_id)->value('email');
                Mail::to($userEmail)->send(new ZoomMeetingInvitation($meetingDetails['join_url'], $meetingDetails['password']));
            }
            return 'Zoom meetings scheduled and invitations sent successfully.';
        } catch (Exception $e) {
            return 'Failed to schedule Zoom meetings.';
        }
    }
    public function lanchZoomMeeting($appointment_id)
    {
        try {
            ZoomMeeting::where('appointment_id', $appointment_id)
                ->where('status', 'waiting')
                ->update(['status' => 'completed']);

            $start_url = ZoomMeeting::where('appointment_id', $appointment_id)
                ->value('start_url');

            return $start_url;
        } catch (Exception $e) {
            return 'Failed to schedule Zoom meetings.';
        }
    }
}
