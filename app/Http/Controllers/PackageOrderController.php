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
        $q->where('status', 'rejected');
    })
    ->get();

    return response()->json([
        'status' => true,
        'data' => $orders,
    ]);
}
public function show($id)
{
    $order = PackageOrder::with([
        'package',
        'trainer.trainerProfile',   // Trainer details + profile
        'trainerSchedule',
        'child.user',               // Child info + user
        'child.parent',             // Child’s parent
        'parent',                   // Parent info
    ])->find($id);

    if (! $order) {
        return response()->json([
            'status' => false,
            'message' => 'الطلب غير موجود.',
        ], 404);
    }

    return response()->json([
        'status' => true,
        'message' => 'تم جلب تفاصيل الطلب بنجاح.',
        'data' => $order,
    ]);
}

}
