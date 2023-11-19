<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doc_type',
        'doc_name',
        'file',
        'doc_orignial_name',
        'doc_id'

    ];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->file);
    }
}
