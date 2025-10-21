<?php

namespace App\Http\Controllers\User\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\StoreOrderRequest;
use App\Models\Child;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipping;
use App\Services\PaymobService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, $product_id): JsonResponse
    {
        try {
            $validated = $request->validated();

            $product = Product::find($product_id);
            if (!$product) {
                return response()->json([
                    'status'  => false,
                    'message' => 'فشل في إنشاء الطلب',
                    'error'   => 'المنتج غير موجود',
                ], 404);
            }

            $shipping = Shipping::find($validated['shipping_id'] ?? null);
            if (!$shipping) {
                return response()->json([
                    'status'  => false,
                    'message' => 'فشل في إنشاء الطلب',
                    'error'   => 'المكان غير موجود',
                ], 404);
            }

            // ✅ Check that the child belongs to the authenticated parent
            if (!empty($validated['children_id'])) {
                $child = Child::find($validated['children_id']);
                if (!$child) {
                    return response()->json([
                        'status' => false,
                        'message' => 'الطفل غير موجود',
                    ], 404);
                }

                if ($child->parent_id !== Auth::id()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'هذا الطفل لا ينتمي إلى الحساب الحالي',
                    ], 403);
                }
            }

            $priceField = $validated['price'] ?? null;
            if (!isset($product->{$priceField})) {
                return response()->json([
                    'status'  => false,
                    'message' => 'فشل في إنشاء الطلب',
                    'error'   => 'حقل السعر المحدد غير صالح لهذا المنتج',
                ], 422);
            }

            $shipPrice = $shipping->price ?? 0;
            $price     = $product->{$priceField} + $shipPrice;

            $image1 = $request->file('image1')?->store('orders', 'public');
            $image2 = $request->file('image2')?->store('orders', 'public');
            $image3 = $request->file('image3')?->store('orders', 'public');

            $order = Order::create([
                'name'             => $validated['name'],
                'children_id'      => $validated['children_id'] ?? null,
                'user_id'          => Auth::id(),
                'product_id'       => $product_id,
                'image1'           => $image1,
                'image2'           => $image2,
                'image3'           => $image3,
                'child_attributes' => $validated['child_attributes'] ?? null,
                'educational_goal' => $validated['educational_goal'] ?? null,
                'price'            => $price,
                'price_type'       => $priceField,
                'shipping_id'      => $validated['shipping_id'],
                'address'          => $validated['address'] ?? null,
                'phone'            => $validated['phone'] ?? null,
                'age'              => $validated['age'] ?? null,
                'gender'           => $validated['gender'] ?? null,
                'status'           => 'pending',
            ]);

            $paymob = new PaymobService();
            $token  = $paymob->authenticate();
            if (!$token) {
                throw new Exception("فشل في الاتصال بـ Paymob");
            }

            $pmOrder = $paymob->createOrder($token, $price * 100, $order->id);
            Log::info('Paymob Create Order Response:', $pmOrder);

            if (!isset($pmOrder['id'])) {
                throw new Exception("فشل في إنشاء طلب الدفع عبر Paymob - Response: " . json_encode($pmOrder));
            }

            $billingData = [
                "apartment"       => "803",
                "email"           => "customer@example.com",
                "floor"           => "42",
                "first_name"      => $order->name,
                "street"          => $order->address,
                "building"        => "2",
                "phone_number"    => $order->phone,
                "shipping_method" => "PKG",
                "postal_code"     => "01898",
                "city"            => $order->shipping->name ?? "Cairo",
                "country"         => "EG",
                "last_name"       => "User",
                "state"           => "Cairo"
            ];

            $paymentKey = $paymob->generatePaymentKey($token, $pmOrder['id'], $price, $billingData);
            if (!isset($paymentKey['token'])) {
                throw new Exception("فشل في إنشاء مفتاح الدفع");
            }

            $iframeId  = env('PAYMOB_IFRAME_ID');
            $iframeUrl = "https://accept.paymob.com/api/acceptance/iframes/$iframeId?payment_token=" . $paymentKey['token'];

            return response()->json([
                'status'  => true,
                'message' => 'تم إنشاء الطلب بنجاح',
                'data'    => [
                    'order'       => $order,
                    'payment_url' => $iframeUrl
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في إنشاء الطلب',
                'error'   => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Order creation error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'فشل في إنشاء الطلب',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function callback(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            Log::info('Paymob Webhook:', $data);

            $transaction       = $data['obj'] ?? null;
            $merchantOrderId   = $transaction['order']['merchant_order_id'] ?? null;
            $paymobOrderId     = $transaction['order']['id'] ?? null;
            $success           = $transaction['success'] ?? false;

            if (!$merchantOrderId) {
                return response()->json(['error' => 'No merchant_order_id'], 400);
            }

            $order = Order::find($merchantOrderId);
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            $order->update([
                'status'           => $success ? 'paid' : 'failed',
                'paymob_order_id' => $paymobOrderId,
            ]);

            return response()->json(['message' => 'ok'], 200);
        } catch (Exception $e) {
            Log::error('Paymob callback error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

public function getMyOrders()
{
    try {
        $userId = Auth::id();

        // جلب الطلبات الخاصة بالمستخدم نفسه أو بأطفاله
        $orders = Order::with([
            'user',
            'child.user',  // جلب الطفل مع بياناته كمستخدم
            'product',
            'shipping'
        ])
        ->where('user_id', $userId)
        ->orWhereHas('child', function ($query) use ($userId) {
            $query->where('parent_id', $userId);
        })
        ->orderBy('id', 'desc')
        ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'لا توجد طلبات لهذا المستخدم',
                'orders' => [],
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم جلب الطلبات بنجاح',
            'orders' => $orders,
        ]);

    } catch (\Exception $e) {
        \Log::error('getMyOrders error: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء جلب الطلبات.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
