<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'text',
        'voice_file'
    ];

    public function options() {
        return $this->hasMany(Option::class);
    }

    public function survey() {
        return $this->hasOne(Survey::class);
    }
}
