<?php

namespace App\Http\Controllers;

use App\Models\PackageOrder;
use Illuminate\Http\Request;

class PackageOrderController extends Controller
{
    public function index()
    {
        $orders = PackageOrder::with([
            'package',
            'trainer',
            'trainerSchedule',
            'child',
            'parent'
        ])->get();

        return response()->json($orders);
    }
}
