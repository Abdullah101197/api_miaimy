<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class AdmissionApplication extends Model
{
    use HasFactory;
    protected $fillable = [
        'application_no',
        'course_name',
        'applied_university',
        'status',
        'missing_documents',
        'application_date',
        'offer_letter',
        'user_id',
        'required_doc'
    ];


    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->offer_letter);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Adjust the relationship type and class name as needed
    }
}
