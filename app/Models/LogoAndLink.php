<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogoAndLink extends Model
{
    protected $fillable = [
        'main_logo',
        'creative_writing_logo',
        'gate_inha_lak_image',
        'gate_start_journey_image',
        'about_page_image',
        'facebook_link',
        'twitter_link',
        'instagram_link',
    ];
}
