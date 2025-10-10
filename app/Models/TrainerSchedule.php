<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'day_of_week',
        'start_time',
        'status',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
