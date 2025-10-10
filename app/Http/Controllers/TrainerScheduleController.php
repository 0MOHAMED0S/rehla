<?php

namespace App\Http\Controllers;

use App\Models\TrainerSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerScheduleController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'day_of_week' => 'required|string|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
        'start_time'  => 'required|date_format:H:i',
    ]);

    $trainerId = Auth::id();
    $day = strtolower($request->day_of_week);
    $startTime = $request->start_time;

    $minutes = (int) date('i', strtotime($startTime));

    $exists = \App\Models\TrainerSchedule::where('trainer_id', $trainerId)
        ->where('day_of_week', $day)
        ->where('start_time', $startTime)
        ->exists();

    if ($exists) {
        return response()->json([
            'status' => false,
            'message' => '❌ لا يمكنك إضافة نفس اليوم ونفس الساعة مرتين.',
        ], 422);
    }

    $firstSchedule = \App\Models\TrainerSchedule::where('trainer_id', $trainerId)
        ->orderBy('id', 'asc')
        ->first();

    if ($firstSchedule) {
        $firstMinutes = (int) date('i', strtotime($firstSchedule->start_time));

        if ($minutes !== $firstMinutes) {
            return response()->json([
                'status' => false,
                'message' => "❌ يجب أن تتطابق الدقائق مع أول موعد سجلته ({$firstMinutes} دقيقة). لا يمكنك اختيار وقت مثل {$startTime}.",
            ], 422);
        }
    }

    $schedule = TrainerSchedule::create([
        'trainer_id'  => $trainerId,
        'day_of_week' => $day,
        'start_time'  => $startTime,
        'status'      => 'pending',
    ]);

    return response()->json([
        'status' => true,
        'message' => ' تم إضافة الموعد بانتظار الموافقة.',
        'data' => $schedule,
    ]);
}

public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $schedule = TrainerSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'status' => false,
                'message' => 'الموعد غير موجود',
            ], 404);
        }

        $schedule->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث حالة الموعد بنجاح',
            'data' => $schedule,
        ]);
    }

 public function pending()
    {
        $pendingSchedules = TrainerSchedule::with('trainer')
            ->where('status', 'pending')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'جميع المواعيد التي تنتظر الموافقة',
            'data' => $pendingSchedules,
        ]);
    }
        public function mySchedules()
    {
        $schedules = TrainerSchedule::where('trainer_id', Auth::id())->get();

        return response()->json([
            'status' => true,
            'data' => $schedules,
        ]);
    }
}
