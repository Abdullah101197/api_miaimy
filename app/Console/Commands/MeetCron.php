<?php

namespace App\Console\Commands;

use App\Helpers\ZoomAccesToken;
use App\Models\ZoomMeeting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MeetCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meeting:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("Cron is working fine!");
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
