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
    public function updateNote(Request $request, $order_id): JsonResponse
    {
        try {
            $request->validate([
                'note' => 'required|string|max:2000',
            ]);

            $order = Order::find($order_id);
            if (!$order) {
                return response()->json([
                    'status'  => false,
                    'message' => 'الطلب غير موجود',
                ], 404);
            }

            $order->update([
                'note' => $request->note,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'تم تحديث ملاحظة الطلب بنجاح',
                'order'   => $order,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء تحديث الملاحظة',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
public function showOrderDetails($id)
{
    try {
        $order = Order::with([
            'user',        // ولي الأمر
            'child.user',  // الطفل مع بيانات اليوزر
            'product',
            'shipping'
        ])->find($id);

        if (! $order) {
            return response()->json([
                'status'  => false,
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'تم جلب تفاصيل الطلب بنجاح',
            'order'   => $order, // كل بيانات الطلب زي ما هي
            'parent'  => $order->user ? [
                'id'    => $order->user->id,
                'name'  => $order->user->name,
                'email' => $order->user->email,
            ] : null,
            'child'   => $order->child ? [
                'id'    => $order->child->id,
                'name'  => $order->child->user->name ?? null,
                'age'   => $order->child->age,
                'gender'=> $order->child->gender,
            ] : null,
            'product' => $order->product ? [
                'id'    => $order->product->id,
                'name'  => $order->product->name,
            ] : null,
            'shipping' => $order->shipping ? [
                'id'   => $order->shipping->id,
                'name' => $order->shipping->name,
                'price'=> $order->shipping->price,
            ] : null,
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'فشل في جلب تفاصيل الطلب',
            'error'   => $e->getMessage()
        ], 500);
    }
}


}
