<?php

namespace App\Rules;

use App\Services\Turnstile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;

/**
 * Server-side guard for public forms combining:
 *  1. a time-trap (a human cannot realistically submit faster than a threshold),
 *  2. a randomized-named honeypot (bots tend to fill every field),
 *  3. a Cloudflare Turnstile token verification when the widget is enabled.
 *
 * Fails closed: any tampered/missing field invalidates the request with a single
 * generic message so bots cannot distinguish which check they tripped.
 */
class AntiBot implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $request = app(Request::class);

        if (!$this->timeTrapPasses($request) || !$this->honeypotPasses($request) || !$this->turnstilePasses($request)) {
            $fail('Your submission looks automated. Please try again.');
        }
    }

    protected function timeTrapPasses(Request $request): bool
    {
        $ts = $request->input('hp_ts');

        if (!is_string($ts) || !ctype_digit($ts)) {
            return false;
        }

        $elapsed = time() - (int) $ts;
        $turnstile = app(Turnstile::class);

        if ($elapsed < $turnstile->minHumanSeconds()) {
            return false;
        }

        if ($elapsed > $turnstile->maxHumanMinutes() * 60) {
            return false;
        }

        return true;
    }

    protected function honeypotPasses(Request $request): bool
    {
        // The randomized honeypot field name is delivered with the submission so
        // the server knows which field acts as the trap.
        $name = $request->input('honeypot_name');

        if (!is_string($name) || !preg_match('/^hp_[a-f0-9]{12}$/', $name)) {
            return false;
        }

        // Laravel's ConvertEmptyStringsToNull middleware turns an empty input
        // into null, so treat both null and '' as the "unfilled" (trusted) state.
        $value = $request->input($name);

        return $value === null || $value === '';
    }

    protected function turnstilePasses(Request $request): bool
    {
        $turnstile = app(Turnstile::class);

        if (!$turnstile->enabled()) {
            return true;
        }

        return $turnstile->verify($request->input('turnstile_token'), $request->ip());
    }
}
