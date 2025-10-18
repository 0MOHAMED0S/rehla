<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'trainer_id',
        'trainer_schedule_id',
        'child_id',
        'parent_id',
        'meet_link',
        'sessions',
        'additional_sessions',
        'status',
        'price',
        'completed_sessions'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function trainerSchedule()
    {
        return $this->belongsTo(TrainerSchedule::class, 'trainer_schedule_id');
    }

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}
