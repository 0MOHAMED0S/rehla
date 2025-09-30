<?php

namespace App\Http\Controllers\User\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\PaymobService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Create order and redirect user to Paymob payment iframe.
     */
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

            $priceField = $validated['price'];
            $price = $product->{$priceField} ?? null;
            if ($price === null) {
                return response()->json([
                    'status'  => false,
                    'message' => 'فشل في إنشاء الطلب',
                    'error'   => 'حقل السعر المحدد غير صالح لهذا المنتج',
                ], 422);
            }

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
                'child_attributes' => $validated['child_attributes'],
                'educational_goal' => $validated['educational_goal'],
                'price'            => $price,
                'price_type'       => $priceField,
                'governorate'      => $validated['governorate'],
                'address'          => $validated['address'],
                'phone'            => $validated['phone'],
                'age'              => $validated['age'],
                'gender'           => $validated['gender'],
                'status'           => 'pending',
            ]);

            $paymob = new PaymobService();
            $token  = $paymob->authenticate();
            $pmOrder = $paymob->createOrder($token, $price, $order->id);

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
                "city"            => $order->governorate,
                "country"         => "EG",
                "last_name"       => "User",
                "state"           => "Cairo"
            ];

            $paymentKey = $paymob->generatePaymentKey($token, $pmOrder['id'], $price, $billingData);

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
            return response()->json([
                'status'  => false,
                'message' => 'فشل في إنشاء الطلب',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Paymob Callback (Webhook)
     */
    public function callback(Request $request)
    {
        $data = $request->all();
        Log::info('Paymob Webhook:', $data);
        $transaction = $data['obj'];
        $merchantOrderId = $transaction['order']['merchant_order_id'] ?? null;
        $paymobOrderId   = $transaction['order']['id'] ?? null;
        $success         = $transaction['success'] ?? false;
        if (!$merchantOrderId) {
            return response()->json(['error' => 'No merchant_order_id'], 400);
        }
        $order = Order::find($merchantOrderId);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        if ($success) {
            $order->update([
                'status'           => 'Pending_review',
                'paymob_order_id' => $paymobOrderId,
            ]);
        } else {
            $order->update(['status' => 'Cancelled']);
        }
        return response()->json(['message' => 'ok'], 200);
    }
}
