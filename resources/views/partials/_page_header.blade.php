{{--
    Shared inner-page hero band.

    Parameters (all optional):
      $title       – main heading (defaults to nothing)
      $subtitle    – supporting text
      $imageUrl    – background image URL; falls back to the site hero media
      $eyebrow     – small uppercase kicker shown above the title (e.g. "Stay With Us")
      $icon        – Font Awesome class rendered inside the eyebrow pill (e.g. "fa-hotel")
--}}
@php
    $heroPath = setting('hero_media');
    $bg = $imageUrl
        ?? ($heroPath ? asset('storage/' . $heroPath) : 'https://placehold.co/1920x800/111827/FFFFFF?text=' . rawurlencode('Brickspoint Hotel'));
@endphp

<section class="relative overflow-hidden bg-slate-950">
    <div class="absolute inset-0">
        <img src="{{ $bg }}" alt="" class="h-full w-full object-cover" loading="eager">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/80 via-slate-950/60 to-slate-950/90"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/70 via-transparent to-slate-950/70"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-24 text-center sm:px-6 sm:py-32 lg:px-8 lg:py-36">
        @if(!empty($eyebrow))
            <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.25em] text-amber-300 backdrop-blur">
                @if(!empty($icon))<i class="fas {{ $icon }}"></i>@endif
                {{ $eyebrow }}
            </span>
        @endif

        <h1 class="mt-5 font-brownsugar text-5xl leading-none text-white drop-shadow-lg sm:text-6xl lg:text-7xl">
            {{ $title }}
        </h1>

        @if(!empty($subtitle))
            <p class="mx-auto mt-5 max-w-2xl text-base font-light text-slate-200 sm:text-lg">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="absolute bottom-0 left-0 right-0 h-10 bg-gradient-to-t from-white to-transparent"></div>
</section>