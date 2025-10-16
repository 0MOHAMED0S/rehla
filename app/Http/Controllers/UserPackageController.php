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
        $package = Package::find($packageId);

        if (!$package) {
            return response()->json([
                'status' => false,
                'message' => 'Package not found'
            ], 404);
        }

        // Get price equation (you can later extend this to multiple records)
        $equation = PriceEquation::first();

        if (!$equation) {
            return response()->json([
                'status' => false,
                'message' => 'Price equation not found'
            ], 404);
        }

        $trainers = User::whereHas('trainerSchedules', function ($query) {
                $query->where('status', 'accepted');
            })
            ->with(['trainerSchedules' => function ($query) {
                $query->where('status', 'accepted');
            }])
            ->get();

        $sessions = (int) $package->sessions;
        $base = $equation->base_price;
        $multiplier = $equation->multiplier;
        $extra = 150; // constant in your equation

        $trainers->transform(function ($trainer) use ($base, $multiplier, $extra, $sessions) {
            $trainer->calculated_price = ($base * $multiplier + $extra) * $sessions;
            return $trainer;
        });

        return response()->json([
            'status' => true,
            'package' => [
                'id' => $package->id,
                'name' => $package->name,
                'sessions' => $package->sessions,
            ],
            'data' => $trainers
        ]);
    }
}
