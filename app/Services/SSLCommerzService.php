<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSLCommerzService
{
    protected string $baseUrl;
    protected string $validationUrl;
    protected string $storeId;
    protected string $storePassword;

    public function __construct()
    {
        $this->loadDefaultConfig();
    }

    /**
     * Load default config from services.php / env.
     */
    protected function loadDefaultConfig()
    {
        $isSandbox = config('services.sslcommerz.sandbox', true);
        
        $this->baseUrl = $isSandbox
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
            
        $this->validationUrl = $isSandbox
            ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
            : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';

        $this->storeId = (string) config('services.sslcommerz.store_id');
        $this->storePassword = (string) config('services.sslcommerz.store_password');
    }

    /**
     * Set credentials dynamically for an organization.
     */
    public function useOrganizationConfig(\App\Models\Organization $organization): self
    {
        $settings = $organization->settings['payment_gateways']['sslcommerz'] ?? null;

        if ($settings && ($settings['active'] ?? false)) {
            $isSandbox = $settings['sandbox'] ?? true;
            
            $this->baseUrl = $isSandbox
                ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
                : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
                
            $this->validationUrl = $isSandbox
                ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
                : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';

            // Use organization keys if provided, otherwise fallback to system default (for sandbox only)
            $this->storeId = $settings['store_id'] ?: ($isSandbox ? (string) config('services.sslcommerz.store_id') : '');
            $this->storePassword = $settings['store_password'] ?: ($isSandbox ? (string) config('services.sslcommerz.store_password') : '');
        }

        return $this;
    }

    /**
     * Initiate payment request to SSLCommerz.
     */
    public function initiatePayment(array $data): ?array
    {
        try {
            $postData = array_merge([
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'total_amount' => $data['total_amount'],
                'currency' => 'BDT',
                'tran_id' => $data['tran_id'],
                'success_url' => route('sslcommerz.success'),
                'fail_url' => route('sslcommerz.fail'),
                'cancel_url' => route('sslcommerz.cancel'),
                'ipn_url' => route('sslcommerz.ipn'),
                'cus_name' => $data['cus_name'],
                'cus_email' => $data['cus_email'] ?? 'customer@example.com',
                'cus_phone' => $data['cus_phone'],
                'product_category' => 'Education',
                'product_name' => $data['product_name'],
                'shipping_method' => 'NO',
                'num_of_item' => 1,
                'value_a' => $data['enrollment_id'], // Store enrollment ID in custom field
            ], $data['additional_fields'] ?? []);

            $response = Http::asForm()->withoutVerifying()->post($this->baseUrl, $postData);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['status']) && $result['status'] === 'SUCCESS') {
                    return $result;
                }
                Log::error('SSLCommerz API Error', ['response' => $result]);
            } else {
                Log::error('SSLCommerz Connection Error', ['status' => $response->status(), 'body' => $response->body()]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('SSLCommerz Initiation Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Validate payment with SSLCommerz.
     */
    public function validatePayment(string $valId): ?array
    {
        try {
            $response = Http::withoutVerifying()->get($this->validationUrl, [
                'val_id' => $valId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'format' => 'json'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('SSLCommerz Validation Failed', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('SSLCommerz Validation Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
