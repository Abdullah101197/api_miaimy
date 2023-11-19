<?php

namespace App\Helpers;

use App\Models\ZoomMeeting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class DeleteMeeting
{
    public static function deleteZoomMeeting()
    {
        $expiredMeetings = ZoomMeeting::where('start_time', '<', now())
            ->where('status', '!=', 'completed')
            ->get();
        foreach ($expiredMeetings as $meeting) {

            try {
                $accessToken = ZoomAccesToken::getAccessToken();
                $base_url = config('zoom.base_url');
                $endpoint = $base_url . 'meetings/' . $meeting->meeting_Id;

                $response = Http::withToken($accessToken)
                    ->delete($endpoint);

                if ($response->successful()) {
                    ZoomMeeting::where('meeting_Id', $$meeting->meeting_Id)
                        ->update(['status' => 'deleted']);
                }
            } catch (Exception $e) {
                return 'not delete';
            }
        }
    }
}
