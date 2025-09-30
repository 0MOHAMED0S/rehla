<?php

namespace App\Http\Controllers\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\ShippingRequest;
use App\Http\Requests\Shipping\StoreShippingRequest;
use App\Http\Requests\Shipping\UpdateShippingRequest;
use App\Models\Shipping;
use Exception;
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
    public function store(StoreShippingRequest $request): JsonResponse
    {
        try {
            $Shipping = Shipping::create([
                'name'  => $request->name,
                'price' => $request->price,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'تم إضافة المكان بنجاح.',
                'data'    => $Shipping,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء إضافة المكان.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function update(UpdateShippingRequest $request, $id): JsonResponse
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
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء تعديل سعر الشحن',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
