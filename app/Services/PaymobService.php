<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymobService
{
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.paymob.base_url', env('PAYMOB_BASE_URL'));
        $this->apiKey  = env('PAYMOB_API_KEY');
    }

    // 1. Authentication (Get Token)
    public function authenticate()
    {
        $response = Http::post($this->baseUrl . '/auth/tokens', [
            'api_key' => $this->apiKey
        ]);

        return $response->json()['token'] ?? null;
    }

    // 2. Create Order
    public function createOrder($authToken, $amount, $merchantOrderId)
    {
        $response = Http::post($this->baseUrl . '/ecommerce/orders', [
            'auth_token'      => $authToken,
            'delivery_needed' => false,
            'amount_cents'    => $amount * 100, // Paymob يعمل بالسنت
            'currency'        => 'EGP',
            'merchant_order_id' => $merchantOrderId,
            'items'           => []
        ]);

        return $response->json();
    }

    // 3. Generate Payment Key
    public function generatePaymentKey($authToken, $orderId, $amount, $billingData)
    {
        $response = Http::post($this->baseUrl . '/acceptance/payment_keys', [
            'auth_token' => $authToken,
            'amount_cents' => $amount * 100,
            'expiration'   => 3600,
            'order_id'     => $orderId,
            'billing_data' => $billingData,
            'currency'     => 'EGP',
            'integration_id' => env('PAYMOB_INTEGRATION_ID'),
        ]);

        return $response->json();
    }
}
