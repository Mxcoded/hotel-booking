@extends('layouts.app')

@section('title', 'Brickspoint Boutique Aparthotel - Best Hotel in Wuse II, Abuja')

@section('content')

    {{-- ══ HERO ══ --}}
    <section class="hero-section relative flex min-h-[92vh] items-center justify-center overflow-hidden bg-slate-950 text-white">
        @php
            $heroMedia = setting('hero_media');
            $whatsappDigits = preg_replace('/[^0-9]/', '', setting('whatsapp_number', '+2348099999620'));
        @endphp
        @if ($heroMedia && Str::endsWith($heroMedia, '.mp4'))
            <video autoplay loop muted playsinline class="absolute z-0 h-full w-full object-cover">
                <source src="{{ asset('storage/' . $heroMedia) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        @elseif($heroMedia)
            <div class="absolute inset-0 z-0 bg-cover bg-center"
                style="background-image: url('{{ asset('storage/' . $heroMedia) }}');"></div>
        @else
            <div class="absolute inset-0 z-0 bg-cover bg-center"
                style="background-image: url('https://placehold.co/1920x1080/000000/FFFFFF?text=Stunning+Hotel+View');"></div>
        @endif

        <div class="absolute inset-0 z-[1] bg-gradient-to-b from-slate-950/80 via-slate-950/50 to-slate-950/90"></div>

        <div class="relative z-10 px-4 pb-44 pt-28 text-center max-w-4xl mx-auto">
            <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-amber-300 backdrop-blur sm:text-sm">
                <i class="fas fa-star text-amber-400"></i> Boutique Aparthotel &bull; Wuse II, Abuja
            </span>

            <h1 class="mt-6 font-brownsugar text-5xl leading-none sm:text-7xl lg:text-8xl">
                <span class="block text-amber-400">{{ setting('hero_title', 'Welcome To') }}</span>
                <span class="mt-2 block text-white">Brickspoint</span>
            </h1>

            <p class="mt-4 font-gotham text-base tracking-[0.35em] uppercase text-slate-100 sm:text-2xl">
                {{ setting('slogan', 'Your Home Away From Home...') }}</p>

            <p class="mx-auto mt-4 max-w-2xl text-base font-light text-slate-200 sm:text-xl">
                {{ setting('hero_subtitle', 'No forms. No hassle. Just message us on WhatsApp to reserve your stay.') }}</p>

            <div class="mt-9 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="https://wa.me/{{ $whatsappDigits }}?text=Hi,%20I'd%20like%20to%20book%20a%20room."
                    target="_blank"
                    class="whatsapp-link inline-flex items-center gap-3 rounded-full bg-green-500 px-8 py-4 text-lg font-bold text-white shadow-xl shadow-green-900/30 transition duration-300 ease-in-out transform hover:scale-105 hover:bg-green-600">
                    <i class="fab fa-whatsapp text-2xl"></i> Book Now on WhatsApp
                </a>
                <a href="#rooms"
                    class="inline-flex items-center gap-3 rounded-full border border-white/25 bg-white/10 px-8 py-4 text-lg font-semibold text-white backdrop-blur transition duration-300 ease-in-out hover:bg-white/20">
                    <i class="fas fa-bed text-amber-300"></i> View Rooms &amp; Suites
                </a>
            </div>
        </div>

        {{-- Booking quick-bar --}}
        <div class="absolute inset-x-0 bottom-0 z-20 px-4 pb-6 sm:px-6 lg:px-8">
            <form action="{{ route('rooms') }}" method="GET"
                class="mx-auto grid max-w-4xl grid-cols-1 gap-3 rounded-2xl border border-white/15 bg-slate-900/70 p-4 shadow-2xl backdrop-blur-md sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto] sm:items-end">
                <div>
                    <label for="hero-check-in" class="mb-1 block text-[11px] font-semibold uppercase tracking-widest text-slate-400">Check-in</label>
                    <input type="date" id="hero-check-in" name="check_in"
                        class="js-hero-date w-full rounded-lg border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                </div>
                <div>
                    <label for="hero-check-out" class="mb-1 block text-[11px] font-semibold uppercase tracking-widest text-slate-400">Check-out</label>
                    <input type="date" id="hero-check-out" name="check_out"
                        class="js-hero-date w-full rounded-lg border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                </div>
                <div>
                    <label for="hero-guests" class="mb-1 block text-[11px] font-semibold uppercase tracking-widest text-slate-400">Guests</label>
                    <select id="hero-guests" name="guests"
                        class="w-full rounded-lg border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'Guest' : 'Guests' }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-2.5 text-sm font-bold text-white shadow-glow transition hover:from-amber-600 hover:to-amber-700">
                    <i class="fas fa-magnifying-glass"></i> Check Availability
                </button>
            </form>
        </div>
    </section>

    {{-- ══ TRUST STRIP ══ --}}
    <section class="border-b border-gray-100 bg-slate-950 text-white">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 py-8 sm:px-6 lg:grid-cols-4 lg:px-8">
            <div class="flex items-center gap-3">
                <i class="fas fa-medal text-2xl text-amber-400"></i>
                <div>
                    <p class="text-xl font-bold leading-none">4.8<span class="text-amber-400">/5</span></p>
                    <p class="mt-0.5 text-xs text-slate-400">Guest rating</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fas fa-map-pin text-2xl text-amber-400"></i>
                <div>
                    <p class="text-xl font-bold leading-none">Wuse II</p>
                    <p class="mt-0.5 text-xs text-slate-400">Prime Abuja location</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fas fa-headset text-2xl text-amber-400"></i>
                <div>
                    <p class="text-xl font-bold leading-none">24/7</p>
                    <p class="mt-0.5 text-xs text-slate-400">Concierge &amp; front desk</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i class="fas fa-shield-halved text-2xl text-amber-400"></i>
                <div>
                    <p class="text-xl font-bold leading-none">Secure</p>
                    <p class="mt-0.5 text-xs text-slate-400">Verified online booking</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ FEATURED ROOMS ══ --}}
    <section id="rooms" class="py-20 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-12 max-w-2xl text-center">
                <span class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.25em] text-amber-600">
                    <i class="fas fa-bed"></i> Stay With Us
                </span>
                <h2 class="mt-3 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">Featured Rooms &amp; Suites</h2>
                <p class="mt-3 text-lg text-gray-600">Discover our curated selection of rooms, each designed for ultimate comfort and relaxation.</p>
            </div>

            @if ($featuredRoomTypes->count() > 0)
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @php
                        $usd_rate = (float) setting('usd_exchange_rate', 0);
                    @endphp

                    @foreach ($featuredRoomTypes as $roomType)
                        <div class="reveal group relative overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lift">
                            <div class="relative overflow-hidden">
                                <button onclick="toggleFavorite('roomtype-{{ $roomType->id }}')"
                                    class="favorite-btn absolute right-4 top-4 z-10 rounded-full bg-white/90 p-2.5 shadow transition-transform duration-200 hover:scale-110"
                                    data-room-id="roomtype-{{ $roomType->id }}">
                                    <i class="far fa-heart text-xl text-gray-700"></i>
                                </button>
                                <a href="{{ route('rooms.show', $roomType) }}" class="block overflow-hidden">
                                    <img src="{{ thumbnail_url($roomType->image, 'card') }}" alt="{{ $roomType->name }}"
                                        class="h-64 w-full object-cover transition-transform duration-700 group-hover:scale-110">
                                </a>
                                <span class="absolute bottom-3 left-3 rounded-full bg-slate-900/80 px-3 py-1 text-xs font-semibold text-amber-300 backdrop-blur">
                                    <i class="fas fa-users mr-1"></i> {{ $roomType->base_guests }} Guest{{ $roomType->base_guests > 1 ? 's' : '' }}
                                </span>
                            </div>
                            <div class="p-6">
                                <h3 class="text-2xl font-bold text-gray-900">{{ $roomType->name }}</h3>
                                <div class="mt-3 flex items-end justify-between border-b border-gray-100 pb-4">
                                    <div>
                                        <p class="text-2xl font-bold text-amber-600">₦{{ number_format($roomType->price, 2) }}</p>
                                        <p class="text-xs uppercase tracking-wide text-gray-500">per night</p>
                                    </div>
                                    @if ($usd_rate > 0)
                                        <p class="text-sm text-gray-500">~ ${{ number_format($roomType->price / $usd_rate, 2) }}</p>
                                    @endif
                                </div>
                                <p class="mt-4 line-clamp-2 text-sm text-gray-500">{{ $roomType->description }}</p>
                                <div class="mt-5 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                                    <a href="{{ route('rooms.show', $roomType) }}"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600">
                                        View Details
                                    </a>
                                    <a href="https://wa.me/{{ $whatsappDigits }}?text=Hi,%20I'm%20interested%20in%20the%20{{ urlencode($roomType->name) }}."
                                        target="_blank"
                                        class="whatsapp-link inline-flex items-center justify-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-2.5 text-sm font-semibold text-green-700 transition hover:bg-green-600 hover:text-white">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-12 text-center">
                    <a href="{{ route('rooms') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 px-9 py-4 font-bold text-white shadow-glow transition duration-300 hover:from-amber-600 hover:to-amber-700">
                        View All Rooms <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @else
                <p class="text-center text-gray-600">Our rooms are currently being prepared. Please check back soon!</p>
            @endif
        </div>
    </section>

    {{-- ══ EXPERIENCE / AMENITIES ══ --}}
    <section class="texture-gold-bg py-20 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.25em] text-amber-600"><i class="fas fa-gem"></i> Experience</span>
            <h2 class="mt-3 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">Why Stay With Us?</h2>

            <div class="mt-14 grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
                <div class="reveal flex flex-col items-center rounded-2xl bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lift">
                    <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-2xl text-white shadow-glow">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">24/7 Concierge</h3>
                    <p class="mt-2 text-sm text-gray-600">Always here to help you.</p>
                </div>
                <div class="reveal flex flex-col items-center rounded-2xl bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lift">
                    <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-2xl text-white shadow-glow">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Prime Location</h3>
                    <p class="mt-2 text-sm text-gray-600">In the heart of Abuja.</p>
                </div>
                <div class="reveal flex flex-col items-center rounded-2xl bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lift">
                    <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-2xl text-white shadow-glow">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Free Breakfast</h3>
                    <p class="mt-2 text-sm text-gray-600">Start your day right.<small class="block">T&amp;C apply</small></p>
                </div>
                <div class="reveal flex flex-col items-center rounded-2xl bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lift">
                    <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-2xl text-white shadow-glow">
                        <i class="fas fa-plane-arrival"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Airport Pickup</h3>
                    <p class="mt-2 text-sm text-gray-600">Hassle-free travel.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ ABOUT / EXPERIENCE SPLIT ══ --}}
    <section class="py-20 bg-white">
        <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div class="reveal relative">
                @php
                    $aboutImg = $galleryImages->first()?->path ?? setting('hero_media');
                @endphp
                <div class="overflow-hidden rounded-3xl shadow-lift">
                    <img src="{{ $aboutImg ? asset('storage/' . $aboutImg) : 'https://placehold.co/900x700/1e293b/F59E0B?text=Brickspoint+Wuse' }}"
                        alt="Inside Brickspoint Boutique Aparthotel, Wuse II, Abuja" class="aspect-[4/3] w-full object-cover">
                </div>
                @if ($galleryImages->count() > 1)
                    <div class="absolute -bottom-8 -right-4 hidden w-56 overflow-hidden rounded-2xl border-4 border-white shadow-2xl sm:block">
                        <img src="{{ asset('storage/' . $galleryImages->get(1)->path) }}" alt="Brickspoint experience"
                            class="aspect-square w-full object-cover">
                    </div>
                @endif
                <div class="absolute -top-4 right-6 rounded-full bg-slate-900 px-5 py-2 text-xs font-bold uppercase tracking-widest text-amber-400 shadow-lg">
                    Since 2019
                </div>
            </div>

            <div class="reveal">
                <span class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.25em] text-amber-600"><i class="fas fa-crown"></i> Boutique Living</span>
                <h2 class="mt-3 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">A Home Away From Home in the Heart of Abuja</h2>
                <p class="mt-4 text-lg text-gray-600">{{ $address }}</p>
                <p class="mt-4 text-gray-600">
                    Brickspoint Boutique Aparthotel blends the privacy of a serviced apartment with the service of
                    a five-star hotel. Every suite features a kitchenette, premium bedding and thoughtful touches —
                    minutes from Wuse's business district, restaurants and embassies.
                </p>

                <ul class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ([
                        'fa-kitchen-set' => 'Kitchenette in every suite',
                        'fa-wifi' => 'Free hi-speed Wi-Fi',
                        'fa-utensils' => 'In-house Taste Restaurant',
                        'fa-parking' => 'Ample secure parking',
                        'fa-dumbbell' => 'On-site gym access',
                        'fa-bus' => 'Airport pickup on request',
                    ] as $icon => $label)
                        <li class="flex items-center gap-3 text-gray-700">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600"><i class="fas {{ $icon }}"></i></span>
                            <span class="text-sm font-medium">{{ $label }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('rooms') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 px-7 py-3.5 font-bold text-white shadow-glow transition hover:from-amber-600 hover:to-amber-700">
                        Explore Rooms <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('gallery') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-gray-300 px-7 py-3.5 font-semibold text-gray-800 transition hover:border-amber-500 hover:text-amber-600">
                        <i class="fas fa-images"></i> See Gallery
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ GALLERY STRIP ══ --}}
    @if ($galleryImages->count() > 0)
        <section class="pb-20 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-5">
                    @foreach ($galleryImages as $index => $image)
                        <a href="{{ route('gallery') }}"
                            class="group relative overflow-hidden rounded-xl {{ $index === 0 ? 'col-span-2 row-span-2' : '' }}">
                            <img src="{{ thumbnail_url($image->path, 'card') }}" alt="{{ $image->alt_text }}"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110 {{ $index === 0 ? 'aspect-square lg:aspect-auto lg:h-full' : 'aspect-square' }}">
                            <div class="absolute inset-0 bg-slate-950/0 transition group-hover:bg-slate-950/40"></div>
                            <span class="absolute bottom-3 left-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-amber-600 opacity-0 transition group-hover:opacity-100">
                                <i class="fas fa-expand"></i>
                            </span>
                        </a>
                    @endforeach
                </div>
                <div class="text-center">
                    <a href="{{ route('gallery') }}" class="inline-flex items-center gap-2 font-semibold text-amber-600 hover:text-amber-700">
                        View full gallery <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ══ TESTIMONIALS ══ --}}
    <section class="py-20 bg-slate-950 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-12 max-w-2xl text-center">
                <span class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.25em] text-amber-400"><i class="fas fa-comments"></i> Reviews</span>
                <h2 class="mt-3 text-4xl font-extrabold tracking-tight sm:text-5xl">What Our Guests Say</h2>
            </div>

            @if ($testimonials->count() > 0)
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($testimonials as $testimonial)
                        <div class="reveal flex flex-col rounded-2xl border border-white/10 bg-white/5 p-7 backdrop-blur transition-colors hover:border-amber-400/40">
                            <div class="flex items-center gap-1">
                                @for ($i = 0; $i < 5; $i++)
                                    @if ($i < $testimonial->rating)
                                        <i class="fas fa-star text-amber-400"></i>
                                    @else
                                        <i class="far fa-star text-slate-600"></i>
                                    @endif
                                @endfor
                            </div>
                            <p class="mt-4 flex-grow text-slate-200 italic">"{{ $testimonial->message }}"</p>
                            <div class="mt-6 flex items-center gap-3 border-t border-white/10 pt-5">
                                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-amber-600 font-bold text-white">
                                    {{ strtoupper(substr($testimonial->name ?: 'A', 0, 1)) }}
                                </span>
                                <div>
                                    <p class="font-bold text-white">{{ $testimonial->name ?: 'Anonymous' }}</p>
                                    <p class="text-xs text-slate-400">{{ $testimonial->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mx-auto mt-8 max-w-lg rounded-2xl border border-amber-400/20 bg-amber-400/5 p-8 text-center">
                    <h3 class="text-2xl font-semibold text-amber-300">Your voice matters!</h3>
                    <p class="mt-2 text-slate-300">Be the first to share your experience and inspire others to visit BRICKSPOINT.</p>
                    <button type="button" id="feedback-link-welcome"
                        class="mt-6 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-3 font-medium text-white shadow-glow transition hover:from-amber-600 hover:to-amber-700">
                        <i class="fas fa-comment-dots"></i> Leave Feedback
                    </button>
                </div>
            @endif
        </div>
    </section>

    {{-- ══ CONTACT & LOCATION ══ --}}
    <section id="contact" class="py-20 bg-slate-50">
        <div class="mx-auto grid max-w-7xl grid-cols-1 items-start gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div class="reveal">
                <span class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.25em] text-amber-600"><i class="fas fa-location-dot"></i> Find Us</span>
                <h2 class="mt-3 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">Contact &amp; Location</h2>
                <p class="mt-4 text-lg text-gray-600">{{ $address }}</p>

                <ul class="mt-6 space-y-4 text-gray-700">
                    <li class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600"><i class="fas fa-envelope"></i></span>
                        <a href="mailto:{{ $email }}" class="font-medium hover:text-amber-600">{{ $email }}</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600"><i class="fas fa-phone"></i></span>
                        <a href="tel:+2348099999620" class="font-medium hover:text-amber-600">{{ $phone }}</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600"><i class="fab fa-whatsapp"></i></span>
                        <a href="https://wa.me/{{ $whatsappDigits }}?text=Hi,%20I%20have%20a%20question%20for%20Brickspoint%20Wuse." target="_blank"
                            class="whatsapp-link font-medium text-green-700 hover:text-green-800">Chat With Us on WhatsApp</a>
                    </li>
                </ul>

                <h3 class="mt-10 mb-5 text-2xl font-bold text-gray-900">Or Send Us a Message</h3>

                @if (session('success'))
                    <div class="mb-4 flex items-start gap-3 rounded-xl border-l-4 border-green-500 bg-green-50 p-4 text-green-800" role="alert">
                        <i class="fas fa-check-circle mt-0.5"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                    @csrf
                    @include('partials._antibot')
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="mb-1.5 block text-sm font-semibold text-gray-700">Full Name</label>
                            <input type="text" id="name" name="name"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-semibold text-gray-700">Email Address</label>
                            <input type="email" id="email" name="email"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:outline-none" required>
                        </div>
                    </div>
                    <div>
                        <label for="message" class="mb-1.5 block text-sm font-semibold text-gray-700">Message</label>
                        <textarea id="message" name="message" rows="4"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:outline-none"
                            required></textarea>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 px-7 py-3.5 font-bold text-white shadow-glow transition hover:from-amber-600 hover:to-amber-700">
                        Send Message <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
            <div class="reveal">
                <div class="overflow-hidden rounded-3xl shadow-lift">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d986.1622694438759!2d7.4798507567178305!3d9.083578854700187!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x104e0af63a4024c7%3A0x5c173dd7f1e8dde4!2sBrickspoint!5e0!3m2!1sen!2sng!4v1756256795904!5m2!1sen!2sng"
                        width="100%" height="520" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" class="w-full">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Keep check-out >= check-in +1 in the hero quick-bar
        (function () {
            const dates = document.querySelectorAll('.js-hero-date');
            const checkIn = document.getElementById('hero-check-in');
            const checkOut = document.getElementById('hero-check-out');
            if (!checkIn || !checkOut) return;
            const today = new Date().toISOString().split('T')[0];
            checkIn.min = today;
            checkOut.min = today;
            checkIn.addEventListener('change', function () {
                if (!this.value) return;
                const next = new Date(this.value + 'T00:00:00');
                next.setDate(next.getDate() + 1);
                checkOut.min = next.toISOString().split('T')[0];
                if (!checkOut.value || checkOut.value <= this.value) {
                    checkOut.value = checkOut.min;
                }
            });
        })();
    </script>

@endsection