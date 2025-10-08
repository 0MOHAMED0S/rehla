<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = [
        'name',
        'children_id',
        'user_id',
        'image1',
        'image2',
        'image3',
        'child_attributes',
        'educational_goal',
        'shipping_id',
        'address',
        'phone',
        'age',
        'gender',
        'status',
        'paymob_order_id',
        'price',
        'subscribed_at',
        'expired_at'
    ];
    protected $dates = [
        'subscribed_at',
        'expired_at',
    ];
}
