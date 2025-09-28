<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TermsOfUse extends Model
{
            use HasFactory;

 protected $fillable = [
        'main_title',
        'main_subtitle',
        'section1_title',
        'section1_text',
        'section2_title',
        'section2_text',
    ];
}
