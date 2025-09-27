<?php

namespace App\Http\Controllers\Admin\EnhaLak;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProducController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $products = Product::latest()->paginate(3);

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


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }
            $product = Product::create($data);
            return response()->json([
                'status'  => true,
                'message' => 'تم إنشاء المنتج بنجاح',
                'data'    => $product,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في إنشاء المنتج',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $data = $request->validated();

            // ✅ تحديث الصورة
            if ($request->hasFile('image')) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            // ✅ منطق الأسعار
            $priceFields = ['fixed_price', 'electronic_copy_price', 'printed_copy_price', 'offered_price'];

            // نشوف هل في أي واحدة من أسعار مبعوتة
            $hasPriceField = false;
            foreach ($priceFields as $field) {
                if ($request->has($field)) {
                    $hasPriceField = true;
                    break;
                }
            }

            if ($hasPriceField) {
                if ($request->filled('fixed_price')) {
                    // لو في سعر ثابت → باقي الأسعار Null
                    $data['electronic_copy_price'] = null;
                    $data['printed_copy_price']    = null;
                    $data['offered_price']         = null;
                } else {
                    // لو مفيش سعر ثابت → نخلي الباقي زي ما جاي أو null لو فاضي
                    $data['electronic_copy_price'] = $request->filled('electronic_copy_price') ? $request->input('electronic_copy_price') : null;
                    $data['printed_copy_price']    = $request->filled('printed_copy_price') ? $request->input('printed_copy_price') : null;
                    $data['offered_price']         = $request->filled('offered_price') ? $request->input('offered_price') : null;
                    $data['fixed_price']           = null;
                }
            } else {
                // ما جاش أي نوع سعر → نشيلهم من $data عشان ما يتحدثوش
                unset($data['fixed_price'], $data['electronic_copy_price'], $data['printed_copy_price'], $data['offered_price']);
            }

            $product->update($data);

            return response()->json([
                'status'  => true,
                'message' => 'تم تحديث المنتج بنجاح',
                'data'    => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في تحديث المنتج',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
