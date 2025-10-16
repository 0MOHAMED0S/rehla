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
        'start_time'  => 'required|string|min:0|max:23', // only hour-based input (0–23)
    ]);

    $trainerId = Auth::id();
    $day = strtolower($request->day_of_week);
    $startHour = (int) $request->start_time;

    // Check if trainer already has a schedule at the same hour (ignore rejected)
    $exists = TrainerSchedule::where('trainer_id', $trainerId)
        ->where('day_of_week', $day)
        ->where('status', '!=', 'rejected')
        ->where('start_time', '=', $startHour . ':00')
        ->exists();

    if ($exists) {
        return response()->json([
            'status' => false,
            'message' => '❌ لا يمكنك إضافة نفس اليوم ونفس الساعة مرتين.',
        ], 422);
    }

    // Check for conflicts within one hour (ignore rejected)
    $conflict = TrainerSchedule::where('trainer_id', $trainerId)
        ->where('day_of_week', $day)
        ->where('status', '!=', 'rejected')
        ->whereBetween('start_time', [
            sprintf('%02d:00', max(0, $startHour - 1)),
            sprintf('%02d:59', min(23, $startHour + 1)),
        ])
        ->exists();

    if ($conflict) {
        return response()->json([
            'status' => false,
            'message' => '❌ يجب أن يكون الفرق بين المواعيد ساعة واحدة على الأقل في نفس اليوم.',
        ], 422);
    }

    // Create new schedule (store hour in HH:00 format)
    $schedule = TrainerSchedule::create([
        'trainer_id'  => $trainerId,
        'day_of_week' => $day,
        'start_time'  => sprintf('%02d:00', $startHour),
        'status'      => 'pending',
    ]);

    return response()->json([
        'status' => true,
        'message' => '✅ تم إضافة الموعد بانتظار الموافقة.',
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
