<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashService
{
    protected string $baseUrl;
    protected string $appKey;
    protected string $appSecret;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->loadDefaultConfig();
    }

    /**
     * Load default config from services.php / env.
     */
    protected function loadDefaultConfig()
    {
        $this->baseUrl = config('services.bkash.sandbox') 
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta' 
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';
            
        $this->appKey = config('services.bkash.app_key');
        $this->appSecret = config('services.bkash.app_secret');
        $this->username = config('services.bkash.username');
        $this->password = config('services.bkash.password');
    }

    /**
     * Set credentials dynamically for an organization.
     */
    public function useOrganizationConfig(\App\Models\Organization $organization): self
    {
        $settings = $organization->settings['payment_gateways']['bkash'] ?? null;

        if ($settings && ($settings['active'] ?? false)) {
            $isSandbox = $settings['sandbox'] ?? true;
            
            $this->baseUrl = $isSandbox
                ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta' 
                : 'https://tokenized.pay.bka.sh/v1.2.0-beta';

            // Use organization keys if provided, otherwise fallback to system default (for sandbox only)
            $this->appKey = $settings['app_key'] ?: ($isSandbox ? config('services.bkash.app_key') : '');
            $this->appSecret = $settings['app_secret'] ?: ($isSandbox ? config('services.bkash.app_secret') : '');
            $this->username = $settings['username'] ?: ($isSandbox ? config('services.bkash.username') : '');
            $this->password = $settings['password'] ?: ($isSandbox ? config('services.bkash.password') : '');
        }

        return $this;
    }

    /**
     * Get access token from bKash.
     */
    public function getToken(): ?string
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
                'username' => $this->username,
                'password' => $this->password,
            ])->post("{$this->baseUrl}/tokenized/checkout/token/grant", [
                'app_key' => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            if ($response->successful()) {
                return $response->json('id_token');
            }

            Log::error('bKash Token Grant Failed', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('bKash Token Grant Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create a payment request.
     */
    public function createPayment(string $token, array $data): ?array
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $token,
                'X-App-Key' => $this->appKey,
            ])->post("{$this->baseUrl}/tokenized/checkout/create", [
                'mode' => '0011', // Checkout mode
                'payerReference' => $data['payerReference'] ?? '01XXXXXXXXX',
                'callbackURL' => route('bkash.callback'),
                'amount' => $data['amount'],
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $data['invoiceNumber'],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('bKash Create Payment Failed', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('bKash Create Payment Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Execute the payment after callback.
     */
    public function executePayment(string $token, string $paymentID): ?array
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $token,
                'X-App-Key' => $this->appKey,
            ])->post("{$this->baseUrl}/tokenized/checkout/execute", [
                'paymentID' => $paymentID,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('bKash Execute Payment Failed', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('bKash Execute Payment Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Query payment status.
     */
    public function queryPayment(string $token, string $paymentID): ?array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $token,
                'X-App-Key' => $this->appKey,
            ])->post("{$this->baseUrl}/tokenized/checkout/payment/status", [
                'paymentID' => $paymentID,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
