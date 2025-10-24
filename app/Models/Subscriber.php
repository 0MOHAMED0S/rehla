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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }

    public function subscribeDetail()
    {
        return $this->belongsTo(SubscribeDetails::class, 'subscribe_id', 'id');
    }
}
