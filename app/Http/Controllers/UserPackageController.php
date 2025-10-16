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


}
