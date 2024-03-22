<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Ending extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'text',
        'voice_file'
    ];

    public function survey() {
        return $this->belongsTo(Survey::class);
    }

    public function voice_file_url() {
        return Storage::url("users/{$this->survey->user->id}/{$this->voice_file}");
    }
}
