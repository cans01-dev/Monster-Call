<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'dial',
        'next_faq_id'
    ];

    public function next_faq() {
        return $this->belongsTo(Faq::class);
    }
    
    public function next_ending() {
        return $this->belongsTo(Ending::class);
    }
}
