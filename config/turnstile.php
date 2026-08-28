<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile
    |--------------------------------------------------------------------------
    |
    | Turnstile protects public forms from bots. When enabled, the widget is
    | rendered on public forms and every submission must include a valid token
    | verified against Cloudflare's siteverify endpoint.
    |
    | Keys can be provided via .env (TURNSTILE_SITE_KEY / TURNSTILE_SECRET_KEY)
    | or via the admin Settings (setting('turnstile_site_key') etc.). The admin
    | Settings values take precedence so teams can rotate keys without deploys.
    |
    | The "challenge" auto mode lets Cloudflare decide whether to render an
    | interactive challenge, a non-interactive checkbox, or nothing at all.
    |
    */

    'enabled' => env('TURNSTILE_ENABLED', false),

    'site_key' => env('TURNSTILE_SITE_KEY'),

    'secret_key' => env('TURNSTILE_SECRET_KEY'),

    'challenge' => 'auto', // auto | managed | non-interactive | invisible

    'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',

    'min_human_seconds' => 3,

    'max_human_minutes' => 8,
];
