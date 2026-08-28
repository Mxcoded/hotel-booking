<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Paystack
{
    public function enabled(): bool
    {
        return filter_var(setting('paystack_enabled', false), FILTER_VALIDATE_BOOLEAN)
            && $this->secretKey() !== null;
    }

    public function secretKey(): ?string
    {
        return setting('paystack_secret_key') ?: null;
    }

    public function publicKey(): ?string
    {
        return setting('paystack_public_key') ?: null;
    }

    public function currency(): string
    {
        return setting('paystack_currency', 'NGN');
    }

    public function baseUrl(): string
    {
        return 'https://api.paystack.co';
    }

    /**
     * Call Paystack's initialize endpoint to start a hosted checkout.
     * Amount is in naira; Paystack expects kobo for NGN.
     *
     * @return array{status: bool, authorization_url: ?string, reference: ?string, message: ?string}
     */
    public function initialize(array $params): array
    {
        if (!$this->enabled()) {
            return ['status' => false, 'authorization_url' => null, 'reference' => null, 'message' => 'Paystack is not enabled.'];
        }

        $amount = isset($params['amount']) ? (int) round(((float) $params['amount']) * 100) : 0;

        $payload = array_merge($params, [
            'amount' => $amount,
            'currency' => $this->currency(),
        ]);

        $response = Http::withToken($this->secretKey())
            ->acceptJson()
            ->post($this->baseUrl() . '/transaction/initialize', $payload);

        if ($response->failed()) {
            return [
                'status' => false,
                'authorization_url' => null,
                'reference' => null,
                'message' => $response->json('message', 'Paystack could not be reached.'),
            ];
        }

        $data = (array) $response->json('data');

        return [
            'status' => (bool) $response->json('status'),
            'authorization_url' => $data['authorization_url'] ?? null,
            'reference' => $data['reference'] ?? null,
            'message' => $response->json('message'),
        ];
    }

    /**
     * Verify a transaction reference.
     *
     * @return array{status: bool, paid: bool, reference: ?string, amount: float, message: ?string}
     */
    public function verify(string $reference): array
    {
        if (!$this->enabled()) {
            return ['status' => false, 'paid' => false, 'reference' => $reference, 'amount' => 0, 'message' => 'Paystack is not enabled.'];
        }

        $response = Http::withToken($this->secretKey())
            ->acceptJson()
            ->get($this->baseUrl() . '/transaction/verify/' . urlencode($reference));

        if ($response->failed()) {
            return [
                'status' => false,
                'paid' => false,
                'reference' => $reference,
                'amount' => 0,
                'message' => $response->json('message', 'Verification failed.'),
            ];
        }

        $data = (array) $response->json('data');

        return [
            'status' => (bool) $response->json('status'),
            'paid' => ($data['status'] ?? null) === 'success',
            'reference' => $data['reference'] ?? $reference,
            'amount' => isset($data['amount']) ? ((float) $data['amount']) / 100 : 0,
            'message' => $response->json('message'),
        ];
    }
}
