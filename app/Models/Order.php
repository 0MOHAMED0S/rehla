<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'children_id',
        'user_id',
        'product_id',
        'image1',
        'image2',
        'image3',
        'child_attributes',
        'educational_goal',
        'price',
        'governorate',
        'address',
        'phone',
        'age',
        'gender',
        'status',
        'paymob_order_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function child()
    {
        return $this->belongsTo(User::class, 'children_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
