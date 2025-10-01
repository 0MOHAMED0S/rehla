<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'age',
        'gender',
        'interests',
        'strengths',
        'avatar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
    public function childUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
