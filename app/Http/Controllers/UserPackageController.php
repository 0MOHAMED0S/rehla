<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Package;
use App\Models\PackageOrder;
use App\Models\PriceEquation;
use App\Models\TrainerSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserPackageController extends Controller
{
public function index()
{
    // ✅ Get price equation
    $priceEquation = PriceEquation::latest()->first();

    if (! $priceEquation) {
        return response()->json([
            'status' => false,
            'message' => 'لم يتم العثور على معادلة السعر.',
        ]);
    }

    $base = $priceEquation->base_price;
    $mult = $priceEquation->multiplier;

    // ✅ Get min & max trainer prices
    $minTrainerPrice = \App\Models\TrainerProfile::min('price');
    $maxTrainerPrice = \App\Models\TrainerProfile::max('price');

    // ✅ Get all active packages
    $packages = Package::where('status', 1)->get([
        'id',
        'name',
        'sessions',
        'features',
        'is_most_popular',
    ]);

    // ✅ Add calculated min/max price for each package
    $packages->transform(function ($package) use ($base, $mult, $minTrainerPrice, $maxTrainerPrice) {
        $sessions = (int) $package->sessions;

        $minPrice = ($minTrainerPrice * $mult + $base) * $sessions;
        $maxPrice = ($maxTrainerPrice * $mult + $base) * $sessions;

        $package->min_price = round($minPrice, 2);
        $package->max_price = round($maxPrice, 2);

        return $package;
    });

    return response()->json([
        'status' => true,
        'data' => $packages,
    ]);
}

    public function getTrainersByPackage($packageId)
    {
        // Get the package
        $package = Package::find($packageId);

        if (! $package) {
            return response()->json([
                'status' => false,
                'message' => 'Package not found',
            ]);
        }

        // Get the price equation
        $priceEquation = PriceEquation::latest()->first();

        if (! $priceEquation) {
            return response()->json([
                'status' => false,
                'message' => 'Price equation not found',
            ]);
        }

        // Get trainers who have at least one approved schedule
        $trainers = User::whereHas('trainerSchedules', function ($query) {
            $query->where('status', 'approved');
        })
            ->with('trainerProfile')
            ->get();

        // Calculate new price for each trainer
        $trainersData = $trainers->map(function ($trainer) use ($package, $priceEquation) {
            $base = $priceEquation->base_price;
            $mult = $priceEquation->multiplier;
            $sessions = (int) $package->sessions;
            $packagePrice = optional($trainer->trainerProfile)->price ?? 0;

            // ✅ New formula:
            // (package_price * multiplier + base_price) * number_of_sessions
            $calculatedPrice = ($packagePrice * $mult + $base) * $sessions;

            return [
                'id' => $trainer->id,
                'name' => $trainer->name,
                'specialization' => optional($trainer->trainerProfile)->specialization,
                'bio' => optional($trainer->trainerProfile)->bio,
                'image' => optional($trainer->trainerProfile)->image,
                'trainer_base_price' => $packagePrice,
                'base_price' => $base,
                'multiplier' => $mult,
                'sessions' => $sessions,
                'calculated_price' => $calculatedPrice,
            ];
        });

        return response()->json([
            'status' => true,
            'package' => [
                'id' => $package->id,
                'name' => $package->name,
                'sessions' => $package->sessions,
            ],
            'data' => $trainersData,
        ]);
    }
    public function getTrainerSchedules($trainerId)
    {
        $trainer = User::find($trainerId);

        if (! $trainer) {
            return response()->json([
                'status' => false,
                'message' => 'Trainer not found',
            ], 404);
        }

        $schedules = TrainerSchedule::where('trainer_id', $trainerId)
            ->where('status', 'approved')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get(['id', 'day_of_week', 'start_time', 'status']);

        return response()->json([
            'status' => true,
            'trainer' => [
                'id' => $trainer->id,
                'name' => $trainer->name,
            ],
            'data' => $schedules,
        ]);
    }
    public function searchTrainers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|integer|exists:packages,id',
            'day_of_week' => 'nullable|string|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'start_time' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value)) {
                        $fail('يجب أن يكون وقت البداية بصيغة HH:MM مثل 09:30 أو 17:45.');
                    }
                },
            ],
        ], [
            'package_id.required' => 'حقل رقم الباقة مطلوب.',
            'package_id.integer' => 'يجب أن يكون رقم الباقة عددًا صحيحًا.',
            'package_id.exists' => 'الباقة غير موجودة.',
            'day_of_week.in' => 'يوم الأسبوع غير صالح، يجب أن يكون أحد الأيام من السبت إلى الجمعة.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في التحقق من البيانات.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $package = Package::find($request->package_id);
        $priceEquation = PriceEquation::latest()->first();

        if (! $priceEquation) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم العثور على معادلة التسعير.',
            ]);
        }

        $trainers = User::whereHas('trainerSchedules', function ($query) use ($request) {
            $query->where('status', 'approved');

            if ($request->filled('day_of_week')) {
                $query->where('day_of_week', $request->day_of_week);
            }

            if ($request->filled('start_time')) {
                $query->where('start_time', $request->start_time);
            }
        })
            ->with(['trainerProfile', 'trainerSchedules' => function ($query) use ($request) {
                $query->where('status', 'approved');

                if ($request->filled('day_of_week')) {
                    $query->where('day_of_week', $request->day_of_week);
                }

                if ($request->filled('start_time')) {
                    $query->where('start_time', $request->start_time);
                }
            }])
            ->get();

        $trainersData = $trainers->map(function ($trainer) use ($package, $priceEquation) {
            $base = $priceEquation->base_price;
            $mult = $priceEquation->multiplier;
            $sessions = (int) $package->sessions;
            $packagePrice = optional($trainer->trainerProfile)->price ?? 0;

            $calculatedPrice = ($packagePrice * $mult + $base) * $sessions;

            return [
                'id' => $trainer->id,
                'name' => $trainer->name,
                'specialization' => optional($trainer->trainerProfile)->specialization,
                'bio' => optional($trainer->trainerProfile)->bio,
                'image' => optional($trainer->trainerProfile)->image,
                'trainer_base_price' => $packagePrice,
                'base_price' => $base,
                'multiplier' => $mult,
                'sessions' => $sessions,
                'calculated_price' => $calculatedPrice,
                'trainer_schedules' => $trainer->trainerSchedules->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'day_of_week' => $schedule->day_of_week,
                        'start_time' => $schedule->start_time,
                        'status' => $schedule->status,
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => true,
            'filters' => [
                'day_of_week' => $request->day_of_week,
                'start_time' => $request->start_time,
            ],
            'package' => [
                'id' => $package->id,
                'name' => $package->name,
                'sessions' => $package->sessions,
            ],
            'data' => $trainersData,
        ]);
    }

public function store(Request $request)
{
    $parentId = auth()->id();

    $request->validate([
        'package_id' => 'required|exists:packages,id',
        'trainer_id' => 'required|exists:users,id',
        'trainer_schedule_id' => 'required|exists:trainer_schedules,id',
        'child_id' => 'required|exists:children,id',
    ], [
        'package_id.required' => 'يجب اختيار الباقة.',
        'trainer_id.required' => 'يجب اختيار المدرب.',
        'trainer_schedule_id.required' => 'يجب اختيار موعد المدرب.',
        'child_id.required' => 'يجب اختيار الطفل.',
    ]);

    $package = Package::findOrFail($request->package_id);
    $trainer = User::with('trainerProfile')->findOrFail($request->trainer_id);
    $priceEquation = PriceEquation::latest()->first();

    if (! $priceEquation) {
        return response()->json([
            'status' => false,
            'message' => 'لم يتم العثور على معادلة السعر.',
        ]);
    }

    $child = Child::where('id', $request->child_id)
        ->where('parent_id', $parentId)
        ->first();

    if (! $child) {
        return response()->json([
            'status' => false,
            'message' => 'هذا الطفل لا يتبع هذا الوالد.',
        ], 403);
    }

    $schedule = TrainerSchedule::where('id', $request->trainer_schedule_id)
        ->where('trainer_id', $trainer->id)
        ->where('status', 'approved')
        ->first();

    if (! $schedule) {
        return response()->json([
            'status' => false,
            'message' => 'هذا الموعد غير متاح أو لم يتم الموافقة عليه بعد.',
        ], 400);
    }

    $base = $priceEquation->base_price;
    $mult = $priceEquation->multiplier;
    $sessions = (int) $package->sessions;
    $packagePrice = optional($trainer->trainerProfile)->price ?? 0;

    $calculatedPrice = ($packagePrice * $mult + $base) * $sessions;

    $meetLink = 'https://meet.jit.si/' . uniqid('session_');

    $order = PackageOrder::create([
        'package_id' => $package->id,
        'trainer_id' => $trainer->id,
        'trainer_schedule_id' => $schedule->id,
        'child_id' => $child->id,
        'parent_id' => $parentId,
        'meet_link' => $meetLink,
        'sessions' => $sessions,
        'additional_sessions' => 0,
        'price' => $calculatedPrice,
        'status' => 'ongoing',
    ]);

    $schedule->update(['status' => 'rejected']);

    $order->load([
        'package',
        'trainer',
        'trainerSchedule',
        'child.parent',
    ]);

    return response()->json([
        'status' => true,
        'message' => 'تم إنشاء الطلب بنجاح.',
        'data' => $order,
    ], 201);
}



}
