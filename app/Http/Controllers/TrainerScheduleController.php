<?php

namespace App\Http\Controllers;

use App\Models\TrainerSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerScheduleController extends Controller
{
use Carbon\Carbon;
use Illuminate\Http\Request;

public function store(Request $request)
{
    $request->validate([
        'day_of_week' => 'required|string|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
        // قبول إما رقم ساعة أو time string HH:mm أو H
        'start_time'  => 'required',
    ]);

    $trainerId = Auth::id();
    $day = strtolower($request->day_of_week);
    $raw = $request->start_time;

    // 1) نحاول تفسير القيمة: إذا كانت عددية نعتبرها ساعة، وإلا نحاول parse كوقت
    if (is_numeric($raw)) {
        $hour = (int) $raw;
        if ($hour < 0 || $hour > 23) {
            return response()->json(['status' => false, 'message' => 'ساعة غير صحيحة.'], 422);
        }
        $requested = Carbon::createFromTime($hour, 0, 0);
    } else {
        try {
            $c = Carbon::createFromFormat('H:i', $raw);
        } catch (\Exception $e) {
            try {
                $c = Carbon::parse($raw);
            } catch (\Exception $ex) {
                return response()->json(['status' => false, 'message' => 'تنسيق وقت غير صالح. استخدم ساعة (مثال: 15) أو HH:mm.'], 422);
            }
        }
        // تطبيع: نجعل الدقائق = 0 (نخَمِّد الدقائق)
        $requested = $c->copy()->minute(0)->second(0);
    }

    $requestedStr = $requested->format('H:i:s'); // للعرض/الحفظ كـ time

    // 2) تحقق وجود نفس الساعة تمامًا (ونتجاهل 'rejected')
    $exists = TrainerSchedule::where('trainer_id', $trainerId)
        ->where('day_of_week', $day)
        ->where('status', '!=', 'rejected')
        ->whereTime('start_time', $requested->format('H:i:s'))
        ->exists();

    if ($exists) {
        return response()->json([
            'status' => false,
            'message' => '❌ لا يمكنك إضافة نفس اليوم ونفس الساعة مرتين.',
        ], 422);
    }

    // 3) جلب كل المواعيد الصالحة لنفس اليوم (غير مرفوضة) وفحص الفارق بالساعة
    $schedules = TrainerSchedule::where('trainer_id', $trainerId)
        ->where('day_of_week', $day)
        ->where('status', '!=', 'rejected')
        ->get(['start_time']);

    foreach ($schedules as $sch) {
        // تحويل إلى Carbon (نماذج DB مخزنة كـ HH:MM:SS)
        $existing = Carbon::createFromFormat('H:i:s', $sch->start_time);
        $hoursDiff = abs($existing->diffInHours($requested));
        // diffInHours يحسب الفرق بالساعة كاملًا؛ إذا كانت المواعيد نفس الساعة يعطي 0، و10:00 vs 11:00 يعطي 1
        if ($hoursDiff < 1) {
            return response()->json([
                'status' => false,
                'message' => "❌ يوجد تداخل مع ميعاد ({$existing->format('H:i:s')}) — يجب أن يكون الفرق ساعة على الأقل.",
            ], 422);
        }
    }

    // 4) إذا عدنا هنا، نُنشئ الموعد (نخزّن بصيغة HH:MM:SS)
    $schedule = TrainerSchedule::create([
        'trainer_id'  => $trainerId,
        'day_of_week' => $day,
        'start_time'  => $requestedStr,
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
