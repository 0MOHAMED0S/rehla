<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PriceEquation extends Model
{
        use HasFactory;

    protected $fillable = [
        'base_price',
        'multiplier',
    ];
}
