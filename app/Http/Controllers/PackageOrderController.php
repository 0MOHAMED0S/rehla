<?php

namespace App\Http\Controllers;

use App\Models\PackageOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $q->where('status', 'booked');
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
public function myOrders(Request $request)
{
    $user = auth()->user();


    $orders = PackageOrder::with([
        'package',
        'trainer.trainerProfile',
        'trainerSchedule',
        'child.user',          // child's user info
        'child.parent',        // child's parent
        'parent',              // parent info
    ])
    ->where('parent_id', $user->id)
    ->get();

    return response()->json([
        'status'  => true,
        'message' => 'تم جلب طلبات الباقات الخاصة بك بنجاح.',
        'data'    => $orders,
    ]);
}
public function myPackageOrdersForChild()
{
    $user = auth()->user();

    $childProfile = $user->childProfile;

    if (! $childProfile) {
        return response()->json([
            'status'  => false,
            'message' => 'لم يتم العثور على ملف الطفل.',
        ], 404);
    }

    $orders = PackageOrder::with([
        'package',
        'trainer.trainerProfile',
        'trainerSchedule',
        'child.user',
        'child.parent',
        'parent',
    ])
    ->where('child_id', $childProfile->id)
    ->get();

    return response()->json([
        'status'  => true,
        'message' => 'تم جلب الطلبات بنجاح.',
        'data'    => [
            'child_profile' => $childProfile,
            'orders'        => $orders,
        ],
    ]);
}
public function showChildOrder($id)
{
    $user = auth()->user();
    $childProfile = $user->childProfile;

    if (! $childProfile) {
        return response()->json([
            'status'  => false,
            'message' => 'لم يتم العثور على ملف الطفل.',
        ], 404);
    }

    $order = PackageOrder::with([
        'package',
        'trainer.trainerProfile',
        'trainerSchedule',
        'child.user',
        'child.parent',
        'parent',
    ])
    ->where('child_id', $childProfile->id)
    ->find($id);

    if (! $order) {
        return response()->json([
            'status'  => false,
            'message' => 'الطلب غير موجود أو لا يخص هذا الطفل.',
        ], 404);
    }

    return response()->json([
        'status'  => true,
        'message' => 'تم جلب تفاصيل الطلب بنجاح.',
        'data'    => [
            'child_profile' => $childProfile,
            'order'         => $order,
        ],
    ]);
}

public function trainerOrders()
{
    $trainer = Auth::user();

    if (!$trainer || !$trainer->trainerProfile) {
        return response()->json([
            'status' => false,
            'message' => 'You are not authorized or not a trainer.',
        ], 403);
    }

    $orders = PackageOrder::with([
        'package',
        'trainer.trainerProfile',
        'trainerSchedule',
        'child.user',        // child’s account info
        'child.parent',      // parent of the child
        'parent',            // direct parent relation
    ])
    ->where('trainer_id', $trainer->id)
    ->orderBy('created_at', 'desc')
    ->get();

    return response()->json([
        'status' => true,
        'message' => 'Trainer package orders retrieved successfully.',
        'data' => $orders,
    ]);
}

}
