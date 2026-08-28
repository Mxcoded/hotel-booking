<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Turnstile
{
    /**
     * Whether Turnstile is configured for use. Enabled when an explicit flag is
     * set AND a secret key is available (settings table or config/env).
     */
    public function enabled(): bool
    {
        return filter_var($this->setting('enabled', config('turnstile.enabled', false)), FILTER_VALIDATE_BOOLEAN)
            && $this->secretKey() !== null;
    }

    public function siteKey(): ?string
    {
        return $this->setting('site_key', config('turnstile.site_key')) ?: null;
    }

    public function secretKey(): ?string
    {
        return $this->setting('secret_key', config('turnstile.secret_key')) ?: null;
    }

    public function challenge(): string
    {
        return $this->setting('challenge', config('turnstile.challenge', 'auto')) ?: 'auto';
    }

    /**
     * How long a human must take on the form (seconds) before it is accepted.
     */
    public function minHumanSeconds(): int
    {
        return (int) config('turnstile.min_human_seconds', 3);
    }

    /**
     * How long a form may remain open before its timestamp is considered stale.
     */
    public function maxHumanMinutes(): int
    {
        return (int) config('turnstile.max_human_minutes', 8);
    }

    /**
     * Validate a Turnstile widget token against Cloudflare's siteverify API.
     *
     * @param  mixed  $token  The value of the turnstile response field.
     * @param  string|null  $remoteIp  The submitter IP for extra safety (optional).
     */
    public function verify(mixed $token, ?string $remoteIp = null): bool
    {
        if (!$this->enabled()) {
            return true;
        }

        if (!is_string($token) || $token === '') {
            return false;
        }

        $payload = [
            'secret' => $this->secretKey(),
            'response' => $token,
        ];

        if ($remoteIp !== null) {
            $payload['remoteip'] = $remoteIp;
        }

        try {
            $response = Http::acceptJson()
                ->asForm()
                ->post(config('turnstile.verify_url'), $payload);

            return $response->successful() && (bool) ($response->json('success') ?? false);
        } catch (\Throwable $e) {
            // Fail closed on any verification error to avoid letting bots through.
            return false;
        }
    }

    /**
     * Resolve a setting value, preferring the admin settings table so operators
     * can rotate keys without redeploying.
     */
    protected function setting(string $key, mixed $fallback = null): mixed
    {
        $setting = \App\Models\Setting::firstWhere('key', 'turnstile_' . $key);

        return $setting !== null ? $setting->value : $fallback;
    }
}
