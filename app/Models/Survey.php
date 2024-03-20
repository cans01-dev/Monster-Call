<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    public function faqs() {
        return $this->hasMany(Faq::class);
    }

    public function endings() {
        return $this->hasMany(Ending::class);
    }
}
