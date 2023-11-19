<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ZoomMeetingInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $joinUrl;
    public $meetingPassword;

    /**
     * Create a new message instance.
     *
     * @param string $joinUrl
     * @param string $meetingPassword
     */
    public function __construct(string $joinUrl, string $meetingPassword)
    {
        $this->joinUrl = $joinUrl;
        $this->meetingPassword = $meetingPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Zoom Meeting Invitation')
                    ->markdown('emails.zoom_meeting_invitation');
    }
}
