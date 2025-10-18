<?php

namespace App\Http\Controllers;

use App\Models\TrainerSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerScheduleController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required|string|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'start_time'  => 'required|string',
        ]);

        $trainerId = Auth::id();
        $day = strtolower($request->day_of_week);
        $rawTime = trim($request->start_time);

        try {
            $requested = Carbon::parse($rawTime);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'تنسيق الوقت غير صالح. استخدم وقتًا مثل 03:15 PM أو 15:15.',
            ], 422);
        }

        $requestedStr = $requested->format('H:i:s');

        $exists = TrainerSchedule::where('trainer_id', $trainerId)
            ->where('day_of_week', $day)
            ->where('status', '!=', 'rejected')
            ->whereTime('start_time', '=', $requestedStr)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => '❌ لا يمكنك إضافة نفس اليوم ونفس الوقت مرتين.',
            ], 422);
        }

        $schedules = TrainerSchedule::where('trainer_id', $trainerId)
            ->where('day_of_week', $day)
            ->where('status', '!=', 'rejected')
            ->get(['start_time']);

        foreach ($schedules as $sch) {
            $existing = Carbon::createFromFormat('H:i:s', $sch->start_time);
            $minutesDiff = abs($existing->diffInMinutes($requested));

            if ($minutesDiff < 60) {
                return response()->json([
                    'status' => false,
                    'message' => "❌ يوجد تداخل مع ميعاد ({$existing->format('H:i')}) — يجب أن يكون الفارق على الأقل ساعة واحدة.",
                ], 422);
            }
        }

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

    public function all()
    {
        $sessions = TrainerSchedule::with('trainer')
            ->select('id', 'trainer_id', 'day_of_week', 'start_time', 'status')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'trainer' => $session->trainer->name ?? 'غير معروف',
                    'day' => $session->day_of_week,
                    'time' => date('H:i', strtotime($session->start_time)),
                    'status' => $session->status,
                ];
            });
        return response()->json($sessions);
    }

    public function profile()
    {
        $user = Auth::user();

        if (!$user || !$user->trainerProfile) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم العثور على ملف المدرب',
            ], 404);
        }

        $profile = $user->trainerProfile;

        return response()->json([
            'status' => true,
            'data' => [
                'name' => $user->name,
                'specialization' => $profile->specialization ?? '',
                'slug' => $profile->slug ?? '',
                'bio' => $profile->bio ?? '',
                'price' => $profile->price ?? '',
                'image' => $profile->image ? asset('storage/' . $profile->image) : null,
            ],
        ]);
    }

    public function approved()
{
    $schedules = TrainerSchedule::where('status', 'approved')->get();

    return response()->json([
        'status' => true,
        'data' => $schedules,
    ]);
}

}
