<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'home_address',
        'date_of_birth',
        'nic',
        'nic_expire_date',
        'passport_no',
        'passport_expire_date',
        'religion',
        'profile_picture',
        'father_name',
        'phone'
    ];


    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->profile_picture);
    }
  
   
}
