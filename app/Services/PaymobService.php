<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;

class PaymobService
{
    protected $baseUrl;
    protected $apiKey;
    protected $integrationId;

    public function __construct()
    {
        $this->baseUrl       = "https://accept.paymob.com/api";
        $this->apiKey        = env("PAYMOB_API_KEY");
        $this->integrationId = env("PAYMOB_INTEGRATION_ID"); // لازم يكون عندك في .env
    }

    /**
     * Authenticate and get token
     */
    public function authenticate()
    {
        try {
            $response = Http::post("{$this->baseUrl}/auth/tokens", [
                "api_key" => $this->apiKey
            ]);

            $data = $response->json();
            Log::info("Paymob Auth Response: ", $data);

            return $data['token'] ?? null;
        } catch (Exception $e) {
            Log::error("Paymob Auth Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create Order in Paymob
     */
    public function createOrder($token, $amount, $merchantOrderId)
    {
        try {
            // توليد merchant_order_id فريد عشان نتجنب مشكلة duplicate
            $uniqueOrderId = $merchantOrderId . '-' . Str::uuid();

            $response = Http::withToken($token)->post("{$this->baseUrl}/ecommerce/orders", [
                "auth_token"        => $token,
                "delivery_needed"   => "false",
                "amount_cents"      => (int)($amount * 100), // السعر بالقروش
                "currency"          => "EGP",
                "merchant_order_id" => $uniqueOrderId,
                "items"             => []
            ]);

            $data = $response->json();
            Log::info("Paymob Create Order Response:", $data);

            return $data;
        } catch (Exception $e) {
            Log::error("Paymob Create Order Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate Payment Key
     */
    public function generatePaymentKey($token, $orderId, $amount, $billingData)
    {
        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/acceptance/payment_keys", [
                "auth_token" => $token,
                "amount_cents" => (int)($amount * 100),
                "expiration" => 3600,
                "order_id" => $orderId,
                "billing_data" => $billingData,
                "currency" => "EGP",
                "integration_id" => $this->integrationId,
                "lock_order_when_paid" => "true"
            ]);

            $data = $response->json();
            Log::info("Paymob Payment Key Response:", $data);

            return $data;
        } catch (Exception $e) {
            Log::error("Paymob Payment Key Error: " . $e->getMessage());
            return null;
        }
    }
}
