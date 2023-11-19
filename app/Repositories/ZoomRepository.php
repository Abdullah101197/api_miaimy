<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\User;
use App\Models\ZoomMeeting;
use DateTime;
use Illuminate\Support\Facades\Hash;

class ZoomRepository
{

    function storeZoomMeeting($upcomingAppointments, $meetingDetails)
    {
        $startTime = new DateTime($meetingDetails['start_time']);
        $formattedStartTime = $startTime->format('Y-m-d H:i:s');
        foreach ($upcomingAppointments as $key => $value) {
            $zoomMeeting = new ZoomMeeting([
                'appointment_id' => $value->id,
                'user_id' => $value->user_id,
                'uuid' => $meetingDetails['uuid'],
                'meeting_id' => $meetingDetails['id'],
                'host_id' => $meetingDetails['host_id'],
                'host_email' => $meetingDetails['host_email'],
                'topic' => $meetingDetails['topic'],
                'type' => 2,
                'status' => $meetingDetails['status'],
                'start_time' => $formattedStartTime,
                'duration' => 60,
                'timezone' => $meetingDetails['timezone'],
                'start_url' => $meetingDetails['start_url'],
                'join_url' => $meetingDetails['join_url'],
                'password' => $meetingDetails['password'],
                'h323_password' => $meetingDetails['h323_password'],
                'pstn_password' => $meetingDetails['pstn_password'],
                'encrypted_password' => $meetingDetails['encrypted_password'],
                'settings' => [
                    'host_video' => false,
                ],
                'pre_schedule' => false,
            ]);

            $zoomMeeting->save();

            Appointment::where('id', $value->id)->update(['join_url' => $meetingDetails['join_url'], 'meeting_status' => 1]);
        }
    }
}
