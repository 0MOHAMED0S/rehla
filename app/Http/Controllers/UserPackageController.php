<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PriceEquation;
use App\Models\TrainerSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class UserPackageController extends Controller
{
    public function index()
    {
        $packages = Package::where('status', 1)->get([
            'id',
            'name',
            'sessions',
            'features',
            'is_most_popular',
        ]);

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
    $request->validate([
        'package_id' => 'required|integer|exists:packages,id',
        'day_of_week' => 'nullable|string|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
        'start_time' => [
            'nullable',
            'date_format:H:i',
            function ($attribute, $value, $fail) {
                // Check that the time is within a valid 24-hour range
                $timestamp = strtotime($value);
                if ($timestamp === false) {
                    $fail('The time format is invalid.');
                } else {
                    $hours = (int) date('H', $timestamp);
                    $minutes = (int) date('i', $timestamp);
                    if ($hours < 0 || $hours > 23 || $minutes < 0 || $minutes > 59) {
                        $fail('The start time must be a valid 24-hour time.');
                    }
                }
            }
        ],
    ]);

    $package = Package::find($request->package_id);
    $priceEquation = PriceEquation::latest()->first();

    if (! $priceEquation) {
        return response()->json([
            'status' => false,
            'message' => 'Price equation not found',
        ]);
    }

    // Build the query for trainers dynamically
    $trainers = User::whereHas('trainerSchedules', function ($query) use ($request) {
        $query->where('status', 'approved');

        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        if ($request->filled('start_time')) {
            $query->where('start_time', $request->start_time);
        }
    })
        ->with('trainerProfile')
        ->get();

    // Calculate trainer price using your equation
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

}
