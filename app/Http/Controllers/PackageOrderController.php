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
        'trainer.trainerProfile',
        'trainerSchedule',
        'child.user',
        'parent',
    ])
    ->whereHas('trainerSchedule', function ($q) {
        $q->where('status', 'approved');
    })
    ->get();

    return response()->json([
        'status' => true,
        'data' => $orders,
    ]);
}

}
