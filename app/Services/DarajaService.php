<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DarajaService
{
    public function stkPush(array $payload): array
    {
        $baseUrl = rtrim((string) config('services.mpesa.base_url'), '/');
        $shortcode = (string) config('services.mpesa.shortcode');
        $passkey = (string) config('services.mpesa.passkey');
        $callbackUrl = $this->normalizeCallbackUrl((string) config('services.mpesa.callback_url'));
        $transactionType = (string) config('services.mpesa.transaction_type', 'CustomerPayBillOnline');

        if ($shortcode === '' || $passkey === '' || $callbackUrl === '') {
            throw new \RuntimeException('Missing M-Pesa shortcode, passkey, or callback URL.');
        }

        $timestamp = now()->format('YmdHis');
        $password = base64_encode($shortcode.$passkey.$timestamp);
        $token = $this->accessToken();

        $response = Http::baseUrl($baseUrl)
            ->withToken($token)
            ->acceptJson()
            ->post('/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => $shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => $transactionType,
                'Amount' => (int) ($payload['amount'] ?? 0),
                'PartyA' => (string) ($payload['phone'] ?? ''),
                'PartyB' => $shortcode,
                'PhoneNumber' => (string) ($payload['phone'] ?? ''),
                'CallBackURL' => $callbackUrl,
                'AccountReference' => (string) ($payload['account_reference'] ?? 'Donation'),
                'TransactionDesc' => (string) ($payload['transaction_desc'] ?? 'Donation Payment'),
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Daraja STK request failed: '.$response->body());
        }

        return $response->json();
    }

    private function normalizeCallbackUrl(string $url): string
    {
        $normalized = trim($url);
        if ($normalized === '') {
            return '';
        }

        // Guard against base-domain values by forcing the callback route path.
        if (parse_url($normalized, PHP_URL_PATH) === null || parse_url($normalized, PHP_URL_PATH) === '/') {
            $normalized = rtrim($normalized, '/').'/mpesa/callback';
        }

        return $normalized;
    }

    private function accessToken(): string
    {
        $baseUrl = rtrim((string) config('services.mpesa.base_url'), '/');
        $consumerKey = (string) config('services.mpesa.consumer_key');
        $consumerSecret = (string) config('services.mpesa.consumer_secret');

        if ($consumerKey === '' || $consumerSecret === '') {
            throw new \RuntimeException('Missing M-Pesa consumer key or consumer secret.');
        }

        $response = Http::baseUrl($baseUrl)
            ->withBasicAuth($consumerKey, $consumerSecret)
            ->acceptJson()
            ->get('/oauth/v1/generate', [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Failed to get Daraja access token: '.$response->body());
        }

        $token = (string) data_get($response->json(), 'access_token', '');
        if ($token === '') {
            throw new \RuntimeException('Daraja response did not include access_token.');
        }

        return $token;
    }
}
