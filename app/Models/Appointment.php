<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'date',
        'time',
        'consultant_name',
        'agenda',
        'status',
        'join_url',
        'meeting_status'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
