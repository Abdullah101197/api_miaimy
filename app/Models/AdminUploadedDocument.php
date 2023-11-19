<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class AdminUploadedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_no',
        'doc_type',
        'doc_name',
        'doc_path',
    ];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->doc_path);
    }
}
