<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'text',
        'order_num',
        'voice_file'
    ];

    public function options() {
        return $this->hasMany(Option::class);
    }

    public function survey() {
        return $this->belongsTo(Survey::class);
    }

    public function voice_file_url() {
        return Storage::url("users/{$this->survey->user->id}/{$this->voice_file}");
    }
}
