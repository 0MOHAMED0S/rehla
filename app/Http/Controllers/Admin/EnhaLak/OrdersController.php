<?php

namespace App\Http\Controllers\Admin\EnhaLak;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $orders = Order::with('user:id,name')
                ->select('id', 'user_id', 'price', 'status', 'created_at')
                ->latest()
                ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'تم جلب الطلبات بنجاح',
                'orders' => $orders,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب الطلبات',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
