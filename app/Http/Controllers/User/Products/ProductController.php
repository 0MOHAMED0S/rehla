<?php

namespace App\Http\Controllers\User\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $products = Product::where('status', 1)
                ->latest()
                ->paginate(10);

            return response()->json([
                'status'   => true,
                'message'  => 'تم جلب المنتجات بنجاح',
                'products' => $products,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب المنتجات',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    public function show($id): JsonResponse
    {
        try {
            $product = Product::where('id', $id)
                ->where('status', 1)
                ->first();

            if (!$product) {
                return response()->json([
                    'status'  => false,
                    'message' => 'المنتج غير موجود',
                ], 404);
            }

            return response()->json([
                'status'   => true,
                'message'  => 'تم جلب المنتج بنجاح',
                'product'  => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب المنتج',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
