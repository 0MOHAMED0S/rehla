<?php

namespace App\Http\Controllers\User\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PaymobService;

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

            $priceField = $validated['price'];
            $price = $product->{$priceField} ?? null;

            if ($price === null) {
                return response()->json([
                    'status'  => false,
                    'message' => 'فشل في إنشاء الطلب',
                    'error'   => 'حقل السعر المحدد غير صالح لهذا المنتج',
                ], 422);
            }

            // رفع الصور
            $image1 = $request->file('image1')->store('orders', 'public');
            $image2 = $request->file('image2')->store('orders', 'public');
            $image3 = $request->file('image3')->store('orders', 'public');

            // إنشاء الطلب في DB
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
            ]);

            // 🔹 Paymob Integration
            $paymob = new PaymobService();
            $token  = $paymob->authenticate();
            $pmOrder = $paymob->createOrder($token, $price, $order->id);

            $billingData = [
                "apartment"      => "803",
                "email"          => "customer@example.com",
                "floor"          => "42",
                "first_name"     => $order->name,
                "street"         => $order->address,
                "building"       => "2",
                "phone_number"   => $order->phone,
                "shipping_method" => "PKG",
                "postal_code"    => "01898",
                "city"           => $order->governorate,
                "country"        => "EG",
                "last_name"      => "User",
                "state"          => "Cairo"
            ];

            $paymentKey = $paymob->generatePaymentKey($token, $pmOrder['id'], $price, $billingData);

            $iframeId = env('PAYMOB_IFRAME_ID');
            $iframeUrl = "https://accept.paymob.com/api/acceptance/iframes/$iframeId?payment_token=" . $paymentKey['token'];

            return response()->json([
                'status'  => true,
                'message' => 'تم إنشاء الطلب بنجاح',
                'data'    => [
                    'order'      => $order,
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

    public function callback(Request $request)
{
    $data = $request->all();

    // ✅ تحقق من HMAC
    $hmacSecret = env('PAYMOB_HMAC');
    $calculatedHmac = hash_hmac('sha512', implode('', [
        $data['amount_cents'],
        $data['created_at'],
        $data['currency'],
        $data['error_occured'],
        $data['has_parent_transaction'],
        $data['id'],
        $data['integration_id'],
        $data['is_3d_secure'],
        $data['is_auth'],
        $data['is_capture'],
        $data['is_refunded'],
        $data['is_standalone_payment'],
        $data['is_voided'],
        $data['order'],
        $data['owner'],
        $data['pending'],
        $data['source_data_pan'],
        $data['source_data_sub_type'],
        $data['source_data_type'],
        $data['success']
    ]), $hmacSecret);

    if ($calculatedHmac !== $data['hmac']) {
        return response()->json(['error' => 'Invalid HMAC'], 403);
    }

    $order = Order::where('id', $data['merchant_order_id'])->first();

    if ($data['success'] == "true") {
        $order->update(['status' => 'paid']);
    } else {
        $order->update(['status' => 'failed']);
    }

    return response()->json(['message' => 'ok']);
}

}
