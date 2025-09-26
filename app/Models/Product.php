<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'features_text',
        'electronic_copy_price',
        'printed_copy_price',
        'image',
        'status',
        'offered_price',
        'fixed_price'
    ];
}
