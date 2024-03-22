<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'note',
        'voice_name',
        'success_ending_id',
        'greeting',
        'greeting_voice_file'
    ];

    public function faqs() {
        return $this->hasMany(Faq::class);
    }

    public function endings() {
        return $this->hasMany(Ending::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function greeting_voice_file_url() {
        return Storage::url("users/{$this->user->id}/{$this->greeting_voice_file}");
    }
}
