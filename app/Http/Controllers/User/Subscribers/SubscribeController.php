<?php

namespace App\Http\Controllers\User\Subscribers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscribers\StoreSubscriberRequest;
use App\Models\Shipping;
use App\Models\SubscribeDetails;
use App\Models\Subscriber;
use App\Services\PaymobService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscribeController extends Controller
{
public function store(StoreSubscriberRequest $request): JsonResponse
{
    try {
        $validated = $request->validated();

        $subscribe = SubscribeDetails::first();
        if (!$subscribe) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في إنشاء الاشتراك',
                'error'   => 'لا توجد خطة اشتراك متاحة',
            ], 404);
        }

        $shipping = Shipping::find($validated['shipping_id'] ?? null);
        if (!$shipping) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في إنشاء الاشتراك',
                'error'   => 'المكان غير موجود',
            ], 404);
        }

        $shipPrice = $shipping->price ?? 0;
        $price     = $subscribe->price + $shipPrice;

        $image1 = $request->file('image1')?->store('subscribers', 'public');
        $image2 = $request->file('image2')?->store('subscribers', 'public');
        $image3 = $request->file('image3')?->store('subscribers', 'public');

        $subscriber = Subscriber::create([
            'name'             => $validated['name'],
            'children_id'      => $validated['children_id'] ?? null,
            'user_id'          => Auth::id(),
            'image1'           => $image1,
            'image2'           => $image2,
            'image3'           => $image3,
            'child_attributes' => $validated['child_attributes'] ?? null,
            'educational_goal' => $validated['educational_goal'] ?? null,
            'price'            => $price,
            'shipping_id'      => $validated['shipping_id'],
            'address'          => $validated['address'] ?? null,
            'phone'            => $validated['phone'] ?? null,
            'age'              => $validated['age'] ?? null,
            'gender'           => $validated['gender'] ?? null,
            'status'           => 'pending',
            'subscribed_at'    => now(),
            'expired_at'       => now()->addYear(),
        ]);

        $paymob = new PaymobService();
        $token  = $paymob->authenticate();
        if (!$token) {
            throw new Exception("فشل في الاتصال بـ Paymob");
        }

        $pmOrder = $paymob->createOrder($token, $price * 100, $subscriber->id);
        Log::info('Paymob Create Order Response:', $pmOrder);

        if (!isset($pmOrder['id'])) {
            throw new Exception("فشل في إنشاء طلب الدفع عبر Paymob - Response: " . json_encode($pmOrder));
        }

        $billingData = [
            "apartment"       => "803",
            "email"           => "customer@example.com",
            "floor"           => "42",
            "first_name"      => $subscriber->name,
            "street"          => $subscriber->address,
            "building"        => "2",
            "phone_number"    => $subscriber->phone,
            "shipping_method" => "PKG",
            "postal_code"     => "01898",
            "city"            => $subscriber->shipping->name ?? "Cairo",
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
            'message' => 'تم إنشاء الاشتراك بنجاح',
            'data'    => [
                'subscriber'  => $subscriber,
                'payment_url' => $iframeUrl
            ]
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status'  => false,
            'message' => 'فشل في إنشاء الاشتراك',
            'error'   => $e->errors(),
        ], 422);
    } catch (Exception $e) {
        Log::error('Subscription creation error: ' . $e->getMessage());
        return response()->json([
            'status'  => false,
            'message' => 'فشل في إنشاء الاشتراك',
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

            $subscriber = Subscriber::find($merchantOrderId);
            if (!$subscriber) {
                return response()->json(['error' => 'Subscriber not found'], 404);
            }

            if ($success) {
                $subscriber->update([
                    'status'          => 'subscribed',
                    'paymob_order_id' => $paymobOrderId,
                    'subscribed_at'   => now(),
                    'expired_at'      => now()->addYear(),
                ]);
            } else {
                $subscriber->update([
                    'status'          => 'failed',
                    'paymob_order_id' => $paymobOrderId,
                ]);
            }

            return response()->json(['message' => 'ok'], 200);
        } catch (Exception $e) {
            Log::error('Paymob callback error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
