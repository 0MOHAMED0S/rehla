<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.paymob.base_url', env('PAYMOB_BASE_URL', 'https://accept.paymob.com/api'));
        $this->apiKey  = env('PAYMOB_API_KEY');
    }

    /**
     * 1. Authenticate with Paymob
     */
    public function authenticate(): ?string
    {
        $response = Http::post("{$this->baseUrl}/auth/tokens", [
            'api_key' => $this->apiKey
        ]);

        if (!$response->successful()) {
            Log::error('Paymob Authentication Failed', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
            return null;
        }

        $json = $response->json();
        return $json['token'] ?? null;
    }

    /**
     * 2. Create Order in Paymob
     */
    public function createOrder(string $authToken, int $amount, int $merchantOrderId): array
    {
        $response = Http::post("{$this->baseUrl}/ecommerce/orders", [
            'auth_token'        => $authToken,
            'delivery_needed'   => false,
            'amount_cents'      => $amount * 100, // Paymob expects cents
            'currency'          => 'EGP',
            'merchant_order_id' => $merchantOrderId,
            'items'             => []
        ]);

        $json = $response->json();

        if (!$response->successful() || !isset($json['id'])) {
            Log::error('Paymob CreateOrder Failed', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
        }

        return $json ?? [];
    }

    /**
     * 3. Generate Payment Key
     */
    public function generatePaymentKey(string $authToken, int $orderId, int $amount, array $billingData): array
    {
        $response = Http::post("{$this->baseUrl}/acceptance/payment_keys", [
            'auth_token'     => $authToken,
            'amount_cents'   => $amount * 100,
            'expiration'     => 3600,
            'order_id'       => $orderId,
            'billing_data'   => $billingData,
            'currency'       => 'EGP',
            'integration_id' => env('PAYMOB_INTEGRATION_ID'),
        ]);

        $json = $response->json();

        if (!$response->successful() || !isset($json['token'])) {
            Log::error('Paymob GeneratePaymentKey Failed', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
        }

        return $json ?? [];
    }
}
