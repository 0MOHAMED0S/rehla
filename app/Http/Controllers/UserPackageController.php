<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PriceEquation;
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
        // Get the selected package
        $package = Package::where('status', 1)->find($packageId);

        if (!$package) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم العثور على الباقة المطلوبة أو أنها غير مفعّلة.',
            ], 404);
        }

        // Get the equation
        $equation = PriceEquation::first();

        if (!$equation) {
            return response()->json([
                'status' => false,
                'message' => 'معادلة السعر غير موجودة بعد.',
            ], 404);
        }

        $trainers = User::whereHas('trainerProfile')
            ->whereHas('trainerSchedules', function ($q) {
                $q->where('status', 'accepted');
            })
            ->with(['trainerProfile', 'trainerSchedules' => function ($q) {
                $q->where('status', 'accepted');
            }])
            ->get()
            ->map(function ($trainer) use ($equation, $package) {

                $trainerBasePrice = (float) $trainer->trainerProfile->price;
                $calculated = ($trainerBasePrice * $equation->multiplier + $equation->base_price) * $package->sessions;

                return [
                    'trainer_id' => $trainer->id,
                    'trainer_name' => $trainer->name,
                    'specialization' => $trainer->trainerProfile->specialization ?? '',
                    'bio' => $trainer->trainerProfile->bio ?? '',
                    'image' => $trainer->trainerProfile->image
                        ? asset('storage/' . $trainer->trainerProfile->image)
                        : null,
                    'base_price' => $trainerBasePrice,
                    'total_price' => round($calculated, 2),
                    'accepted_schedules_count' => $trainer->trainerSchedules->count(),
                ];
            });

        return response()->json([
            'status' => true,
            'package' => [
                'id' => $package->id,
                'name' => $package->name,
                'sessions' => $package->sessions,
            ],
            'data' => $trainers,
        ]);
    }
}
