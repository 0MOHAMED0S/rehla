<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscribeDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'image',
        'features',
    ];
    public function subscribers()
    {
        return $this->hasMany(Subscriber::class, 'subscribe_id');
    }
}
