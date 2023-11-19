<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function userInfo()
    {
        return $this->hasOne(UserInfo::class);
    }

    public function userDoc()
    {
        return $this->hasOne(UserDocument::class);
    }
    public function userApoint()
    {
        return $this->hasOne(Appointment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function admissionApplication()
    {
        return $this->hasMany(AdmissionApplication::class);
    }
}
