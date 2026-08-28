{{--
    Anti-bot honeypot + Cloudflare Turnstile field set.

    Include once inside every public-facing <form>. It contributes:
      * a random-named honeypot trap (real bots fill it in)
      * a hidden rendered timestamp (human time-trap)
      * a Cloudflare Turnstile widget + token field when enabled

    The corresponding server-side validation is App\Rules\AntiBot.
--}}
@php($antibotName = 'hp_' . bin2hex(random_bytes(6)))
@php($antibotTs = (string) time())

<input type="text" name="{{ $antibotName }}" value="" tabindex="-1" autocomplete="off"
       class="absolute -left-[9999px] w-px h-px opacity-0 pointer-events-none" aria-hidden="true">
<input type="hidden" name="honeypot_name" value="{{ $antibotName }}">
<input type="hidden" name="hp_ts" value="{{ $antibotTs }}">

@if(app(\App\Services\Turnstile::class)->enabled())
    <div class="cf-turnstile" data-sitekey="{{ app(\App\Services\Turnstile::class)->siteKey() }}"
         data-theme="light" data-callback="onTurnstileSuccess"></div>
    <input type="hidden" name="turnstile_token" value="">
@endif
