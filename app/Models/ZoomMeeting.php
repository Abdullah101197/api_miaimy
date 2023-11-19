<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomMeeting extends Model
{
    use HasFactory;
    protected $fillable = [
        'appointment_id',
        'user_id',
        'meeting_id',
        'uuid',
        'host_id',
        'host_email',
        'topic',
        'type',
        'status',
        'start_time',
        'duration',
        'timezone',
        'start_url',
        'join_url',
        'password',
        'h323_password',
        'pstn_password',
        'encrypted_password',
        'settings',
        'pre_schedule',
    ];


    // Define the JSON cast for the 'settings' attribute
    protected $casts = [
        'settings' => 'json',
    ];

    // If you want to disable timestamps for this model, set this property to false
    public $timestamps = true;

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the user associated with the Zoom meeting.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
