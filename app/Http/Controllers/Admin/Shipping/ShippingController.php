<?php

namespace App\Http\Controllers\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\ShippingRequest;
use App\Models\Shipping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $shippings = Shipping::all(['id', 'name', 'price']);

            return response()->json([
                'status'   => true,
                'message'  => 'تم جلب جميع المحافظات وأسعار الشحن بنجاح',
                'shippings' => $shippings,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب المحافظات وأسعار الشحن',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function update(ShippingRequest $request, $id): JsonResponse
    {
        try {
            $shipping = Shipping::find($id);

            if (! $shipping) {
                return response()->json([
                    'status'  => false,
                    'message' => 'المحافظة غير موجودة',
                ], 404);
            }

            $shipping->update($request->validated());

            return response()->json([
                'status'  => true,
                'message' => 'تم تعديل سعر الشحن بنجاح',
                'data'    => $shipping,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء تعديل سعر الشحن',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

}
