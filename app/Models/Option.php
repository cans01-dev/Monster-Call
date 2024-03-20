<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    public function next_faq() {
        return $this->hasOne(Faq::class);
    }
    
    public function next_ending() {
        return $this->hasOne(Ending::class);
    }
}
