<?php

namespace App\Http\Controllers;

use App\Models\PackageOrder;
use Illuminate\Http\Request;

class PackageOrderController extends Controller
{
    public function index()
{
    $orders =PackageOrder::with([
        'package',
        'trainer.trainerProfile',   // ✅ correct trainer relation
        'trainerSchedule',
        'child.user',               // ✅ child’s User info
        'parent',                   // ✅ parent info
    ])
    ->whereHas('trainerSchedule', function ($q) {
        $q->where('status', 'approved');   // ✅ only approved schedules
    })
    ->get();

    return response()->json([
        'status' => true,
        'data' => $orders,
    ]);
}

}
