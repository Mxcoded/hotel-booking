@php
$structuredData = [
    '@context' => 'https://schema.org',
    '@type' => 'Hotel',
    'name' => 'Brickspoint Boutique Aparthotel',
    'description' => 'Luxury serviced apartments in the heart of Abuja, offering premium hospitality for business and leisure travelers.',
    'image' => asset('storage/' . setting('seo_logo')),
    'telephone' => setting('phone_number', '+2348099999620'),
    'url' => route('home'),
    'priceRange' => '₦173,250.00 - ₦500,000.00',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => '11 Adzope Crescent',
        'addressLocality' => 'Wuse II',
        'addressRegion' => 'Abuja',
        'postalCode' => '900288',
        'addressCountry' => [
            '@type' => 'Country',
            'name' => 'NG',
        ],
    ],
    'geo' => [
        '@type' => 'GeoCoordinates',
        'latitude' => '9.072264',
        'longitude' => '7.491302',
    ],
    'checkinTime' => '14:00',
    'checkoutTime' => '12:00',
    'amenityFeature' => [
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Free Wi-Fi', 'value' => true],
        ['@type' => 'LocationFeatureSpecification', 'name' => '24/7 Front Desk', 'value' => true],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Airport Shuttle', 'value' => true],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Swimming Pool', 'value' => false],
        // --- NEW AMENITIES FROM YOUR KEYWORD LIST ---
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Kitchenette', 'value' => true],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Gym Access', 'value' => false],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Med Spa', 'value' => false],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Complimentary Breakfast', 'value' => true],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'In House Restaurant (Taste Restaurant)', 'value' => true],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Conference Hall', 'value' => false],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Private Balcony', 'value' => true],
    ],
    'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => '4.8',
        'reviewCount' => '234',
    ],
    'hasMap' => 'https://www.google.com/maps/place/11+Adzope+Crescent,+Wuse+II,+Abuja',
    'sameAs' => [
        'https://www.facebook.com/brickspointapartment',
        'https://www.instagram.com/brickspapartment',
        'https://twitter.com/bpaparthotel',
    ],
];
@endphp
@php
    $active = request()->routeIs('home') ? 'home' : (request()->routeIs('rooms') || request()->routeIs('rooms.show') ? 'rooms' : (request()->routeIs('gallery') ? 'gallery' : (request()->routeIs('local-guide') ? 'local-guide' : (request()->routeIs('favorites') ? 'favorites' : (request()->routeIs('menu') ? 'menu' : null)))));
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(app(\App\Services\Turnstile::class)->enabled())
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad" defer></script>
        <script>
            function onTurnstileLoad() { window.turnstileLoaded = true; }
            function onTurnstileSuccess(token) {
                document.querySelectorAll('input[name="turnstile_token"]').forEach(function (f) { f.value = token; });
            }
        </script>
    @endif
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('favicon')) }}">
    <title>@yield('title', 'Brickspoint Boutique Aparthotel - Best Hotel in Wuse II, Abuja')</title>

    <meta name="description"
        content="Book one of the best hotels in Abuja. Brickspoint Boutique Aparthotel offers luxury serviced apartments in Wuse II with a in house Restaurant, Cafe, Lounge. Ideal for business or leisure.">

    <meta name="keywords"
        content="Best hotels in Abuja, Top hotels in Abuja, BricksPoint Wuse booking, BricksPoint Boutique Aparthotel, Hotels in Wuse Abuja, ApartHotel Abuja, Serviced apartments Abuja, Luxury suites in Wuse, Conference hall rental Abuja, Hotel in Wuse, Hotels in Abuja with kitchenette,">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Brickspoint Boutique Aparthotel - Wuse II, Abuja">
    <meta property="og:description"
        content="Book one of the best hotels in Abuja. Brickspoint Boutique Aparthotel offers luxury serviced apartments in Wuse II with a in house Restaurant, Cafe, Lounge. Ideal for business or leisure.">
    <meta property="og:image" content="{{ asset('storage/' . setting('favicon')) }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Brickspoint Boutique Aparthotel - Wuse II, Abuja">
    <meta property="twitter:description"
        content="Book one of the best hotels in Abuja. Brickspoint Boutique Aparthotel offers luxury serviced apartments in Wuse II with a in house Restaurant, Cafe, Lounge. Ideal for business or leisure.">
    <meta property="twitter:image" content="{{ asset('storage/' . setting('favicon')) }}">

    <!-- Tailwind CSS (compiled) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcd34d', 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706', 700: '#b45309', 800: '#92400e', 900: '#78350f' }
                    },
                    boxShadow: {
                        'glow': '0 8px 30px -6px rgba(245,158,11,0.45)',
                        'lift': '0 24px 48px -12px rgba(15,23,42,0.25)'
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Define Custom Local Fonts */
        @font-face {
            font-family: 'BrownSugar';
            src: url("{{ asset('fonts/Brown Sugar .otf') }}") format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'GothamLight';
            src: url("{{ asset('fonts/Gotham-Light.otf') }}") format('opentype');
            font-weight: 300;
            font-style: normal;
        }

        @font-face {
            font-family: 'FuturaLT';
            src: url("{{ asset('fonts/FuturaLT-Light.ttf') }}") format('truetype');
            font-weight: 300;
            font-style: normal;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', -apple-system, 'Segoe UI', Roboto, sans-serif;
            color: #111827;
        }

        .font-brownsugar { font-family: 'BrownSugar', cursive; }
        .font-gotham { font-family: 'GothamLight', 'FuturaLT'; }
        .font-futura { font-family: 'FuturaLT', 'GothamLight'; }

        /* Refined, thin focus rings */
        :focus { outline: none; }
        :focus-visible { outline: 2px solid #d97706; outline-offset: 2px; }

        /* Star rating (feedback) */
        .star-rating input[type="radio"] { display: none; }
        .star-rating label {
            font-size: 2.5rem; color: #d1d5db; cursor: pointer; transition: color 0.2s;
        }
        .star-rating input[type="radio"]:checked~label,
        .star-rating label:hover,
        .star-rating label:hover~label { color: #f59e0b; }

        /* Scroll reveal (respects reduced motion) */
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.7s ease, transform 0.7s ease; }
        .reveal.is-visible { opacity: 1; transform: translateY(0); }
        @media (prefers-reduced-motion: reduce) {
            .reveal { opacity: 1; transform: none; transition: none; }
            html { scroll-behavior: auto; }
        }

        /* Subtle texture used on dark sections (gold grid) */
        .texture-gold-bg {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f59e0b' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>

    @stack('styles')

    <!-- JSON-LD Structured Data for Google -->
    <script type="application/ld+json">
        @json($structuredData)
    </script>
</head>

<body class="bg-white text-gray-800">
    @php
        $whatsappDigits = preg_replace('/[^0-9]/', '', setting('whatsapp_number', '+2348099999620'));
    @endphp

    {{-- ══ Slim top information bar ══ --}}
    <div class="hidden md:block bg-slate-950 text-slate-300 text-xs border-b border-white/5">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex items-center justify-between py-2">
            <div class="flex items-center gap-6">
                <span class="inline-flex items-center gap-1.5"><i class="fas fa-location-dot text-amber-500/80"></i> 11 Adzope Crescent, Wuse II, Abuja</span>
                <a href="tel:+2348099999620" class="inline-flex items-center gap-1.5 hover:text-amber-300 transition-colors"><i class="fas fa-phone text-amber-500/80"></i> +234 809 999 9620</a>
                <a href="mailto:reservations@brickspoint.ng" class="inline-flex items-center gap-1.5 hover:text-amber-300 transition-colors"><i class="fas fa-envelope text-amber-500/80"></i> reservations@brickspoint.ng</a>
            </div>
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-1 text-amber-300/90"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i> 4.8 rated by guests</span>
                <span class="h-3 w-px bg-white/10"></span>
                <div class="flex items-center gap-3">
                    <a href="https://www.facebook.com/brickspointapartment" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="hover:text-amber-300 transition-colors"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/brickspapartment" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="hover:text-amber-300 transition-colors"><i class="fab fa-instagram"></i></a>
                    <a href="https://twitter.com/bpaparthotel" target="_blank" rel="noopener noreferrer" aria-label="Twitter" class="hover:text-amber-300 transition-colors"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Primary header ══ --}}
    <header class="sticky top-0 z-40 bg-slate-900/90 backdrop-blur text-white shadow-lg shadow-slate-900/10">
        <div class="container mx-auto flex justify-between items-center px-4 py-3 md:py-3.5">
            <a href="{{ route('home') }}" class="flex items-center text-xl md:text-2xl font-bold tracking-[0.15rem] md:tracking-[0.2rem]" style="font-family: 'BrownSugar'">
                @if (setting('logo'))
                <img src="{{ asset('storage/' . setting('logo')) }}" alt="Brickspoint Hotel Logo"
                        class="h-9 md:h-12 lg:h-14 w-auto max-w-[130px] md:max-w-[260px] object-contain">
                @else
                    Brickspoint <small class="font-gotham text-xs ml-1">Wuse II</small>
                @endif
            </a>

            <!-- Desktop Menu -->
            <nav class="hidden lg:flex items-center space-x-5 xl:space-x-7 text-sm xl:text-base">
                <a href="{{ route('home') }}" class="transition-colors whitespace-nowrap {{ $active === 'home' ? 'text-amber-400' : 'hover:text-amber-400' }}">
                    <span class="relative py-1 {{ $active === 'home' ? 'border-b-2 border-amber-400' : '' }}">Home</span>
                </a>
                <a href="{{ route('rooms') }}" class="transition-colors whitespace-nowrap {{ $active === 'rooms' ? 'text-amber-400' : 'hover:text-amber-400' }}">
                    <span class="relative py-1 {{ $active === 'rooms' ? 'border-b-2 border-amber-400' : '' }}">Rooms</span>
                </a>
                <a href="{{ route('gallery') }}" class="transition-colors whitespace-nowrap {{ $active === 'gallery' ? 'text-amber-400' : 'hover:text-amber-400' }}">
                    <span class="relative py-1 {{ $active === 'gallery' ? 'border-b-2 border-amber-400' : '' }}">Gallery</span>
                </a>
                <a href="{{ route('local-guide') }}" class="transition-colors whitespace-nowrap {{ $active === 'local-guide' ? 'text-amber-400' : 'hover:text-amber-400' }}">
                    <span class="relative py-1 {{ $active === 'local-guide' ? 'border-b-2 border-amber-400' : '' }}">Explore Wuse II</span>
                </a>
                <a href="{{ route('favorites') }}" class="relative transition-colors whitespace-nowrap {{ $active === 'favorites' ? 'text-amber-400' : 'hover:text-amber-400' }}">
                    <span class="relative py-1 {{ $active === 'favorites' ? 'border-b-2 border-amber-400' : '' }}">Favorites</span>
                    <span id="favorites-count"
                        class="absolute -top-2 -right-5 bg-red-500 text-white text-xs rounded-full h-5 min-w-5 px-1 flex items-center justify-center hidden">0</span>
                </a>
                <a href="{{ route('menu') }}" class="transition-colors whitespace-nowrap {{ $active === 'menu' ? 'text-amber-400' : 'hover:text-amber-400' }}">
                    <span class="relative py-1 {{ $active === 'menu' ? 'border-b-2 border-amber-400' : '' }}">Menu</span>
                </a>
                <a href="{{ route('home') }}#contact" class="transition-colors whitespace-nowrap hover:text-amber-400">
                    <span class="relative py-1">Contact</span>
                </a>

                {{-- Our Hotel dropdown --}}
                <div class="relative group">
                    <button class="flex items-center gap-1 hover:text-amber-400 transition-colors whitespace-nowrap py-1">
                        Our Hotel
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                    </button>
                    <div class="absolute right-0 top-full pt-2 w-60 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="bg-slate-900 border border-slate-700 rounded-xl shadow-2xl overflow-hidden">
                            <div class="px-4 py-2 border-b border-slate-800">
                                <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">Our Branches</span>
                            </div>
                            <a href="{{ route('home') }}"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 hover:text-amber-400 transition-colors group/item border-b border-slate-800/60">
                                <div class="shrink-0 w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center">
                                    <i class="fas fa-location-dot text-amber-400 text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white group-hover/item:text-amber-400 transition-colors">Brickspoint Wuse II</div>
                                    <div class="text-xs text-slate-400">Abuja &mdash; <span class="text-amber-500">Current</span></div>
                                </div>
                            </a>
                            <a href="https://brickspoint.com" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 hover:text-amber-400 transition-colors group/item">
                                <div class="shrink-0 w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center">
                                    <i class="fas fa-location-dot text-slate-400 text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white group-hover/item:text-amber-400 transition-colors">Brickspoint Asokoro</div>
                                    <div class="text-xs text-slate-400">Abuja &bull; brickspoint.com <i class="fas fa-arrow-up-right-from-square text-[10px] ml-0.5"></i></div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                @guest
                    <button type="button" id="feedback-link"
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-sm font-semibold px-4 py-2 shadow-glow transition duration-300 whitespace-nowrap">
                        <i class="fas fa-comment-dots"></i>
                        <span class="hidden xl:inline">Leave Feedback</span>
                    </button>
                @endguest
            </nav>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden flex items-center gap-3">
                <button type="button" id="mobile-menu-button" class="p-2 text-white" aria-label="Open navigation menu">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-slate-900 border-t border-slate-700">
            <div class="flex justify-between items-center px-5 py-3 border-b border-slate-800">
                <span class="text-slate-400 text-xs font-semibold uppercase tracking-widest">Navigation</span>
                <button id="mobile-menu-close" aria-label="Close menu" class="text-slate-400 hover:text-white p-1 -mr-1">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <nav class="flex flex-col py-1">
                <a href="{{ route('home') }}" class="px-5 py-3.5 text-white hover:bg-slate-800 hover:text-amber-400 transition-colors text-base border-b border-slate-800/50 {{ $active === 'home' ? 'text-amber-400 bg-slate-800/50' : '' }}">
                    <i class="fas fa-home w-5 mr-2 text-slate-500"></i>Home
                </a>
                <a href="{{ route('rooms') }}" class="px-5 py-3.5 text-white hover:bg-slate-800 hover:text-amber-400 transition-colors text-base border-b border-slate-800/50 {{ $active === 'rooms' ? 'text-amber-400 bg-slate-800/50' : '' }}">
                    <i class="fas fa-bed w-5 mr-2 text-slate-500"></i>Rooms
                </a>
                <a href="{{ route('gallery') }}" class="px-5 py-3.5 text-white hover:bg-slate-800 hover:text-amber-400 transition-colors text-base border-b border-slate-800/50 {{ $active === 'gallery' ? 'text-amber-400 bg-slate-800/50' : '' }}">
                    <i class="fas fa-images w-5 mr-2 text-slate-500"></i>Gallery
                </a>
                <a href="{{ route('local-guide') }}" class="px-5 py-3.5 text-white hover:bg-slate-800 hover:text-amber-400 transition-colors text-base border-b border-slate-800/50 {{ $active === 'local-guide' ? 'text-amber-400 bg-slate-800/50' : '' }}">
                    <i class="fas fa-map-marker-alt w-5 mr-2 text-slate-500"></i>Explore Wuse II
                </a>
                <a href="{{ route('favorites') }}" class="px-5 py-3.5 text-white hover:bg-slate-800 hover:text-amber-400 transition-colors text-base border-b border-slate-800/50 {{ $active === 'favorites' ? 'text-amber-400 bg-slate-800/50' : '' }}">
                    <i class="fas fa-heart w-5 mr-2 text-slate-500"></i>My Favorites
                </a>
                <a href="{{ route('menu') }}" class="px-5 py-3.5 text-white hover:bg-slate-800 hover:text-amber-400 transition-colors text-base border-b border-slate-800/50 {{ $active === 'menu' ? 'text-amber-400 bg-slate-800/50' : '' }}">
                    <i class="fas fa-utensils w-5 mr-2 text-slate-500"></i>Food Menu
                </a>
                <a href="{{ route('home') }}#contact" class="px-5 py-3.5 text-white hover:bg-slate-800 hover:text-amber-400 transition-colors text-base border-b border-slate-800/50">
                    <i class="fas fa-envelope w-5 mr-2 text-slate-500"></i>Contact
                </a>

                {{-- Our Hotel accordion --}}
                <div class="border-b border-slate-800/50">
                    <button id="mobile-hotel-toggle"
                        class="w-full flex items-center justify-between px-5 py-3.5 text-white hover:bg-slate-800 hover:text-amber-400 transition-colors text-base">
                        <span><i class="fas fa-hotel w-5 mr-2 text-slate-500"></i>Our Hotel</span>
                        <i class="fas fa-chevron-down text-xs text-slate-500 transition-transform duration-200" id="mobile-hotel-chevron"></i>
                    </button>
                    <div id="mobile-hotel-dropdown" class="hidden">
                        <a href="{{ route('home') }}"
                           class="flex items-center gap-3 pl-10 pr-5 py-3 text-slate-300 hover:bg-slate-800 hover:text-amber-400 transition-colors text-sm border-t border-slate-800/40">
                            <i class="fas fa-location-dot text-amber-500 text-xs w-4"></i>
                            <div>
                                <div class="font-medium">Brickspoint Wuse II</div>
                                <div class="text-xs text-slate-500">Abuja &mdash; <span class="text-amber-500">Current</span></div>
                            </div>
                        </a>
                        <a href="https://brickspoint.com" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-3 pl-10 pr-5 py-3 text-slate-300 hover:bg-slate-800 hover:text-amber-400 transition-colors text-sm border-t border-slate-800/40">
                            <i class="fas fa-location-dot text-slate-500 text-xs w-4"></i>
                            <div>
                                <div class="font-medium">Brickspoint Asokoro</div>
                                <div class="text-xs text-slate-500">Abuja &bull; brickspoint.com <i class="fas fa-arrow-up-right-from-square text-[10px]"></i></div>
                            </div>
                        </a>
                    </div>
                </div>

                @guest
                <div class="px-5 py-4 border-t border-slate-800 mt-1">
                    <button type="button" id="mobile-feedback-link"
                        class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-5 py-3 rounded-full shadow-glow transition duration-300">
                        <i class="fas fa-comment-dots"></i>
                        <span class="font-medium">Leave Feedback</span>
                    </button>
                </div>
                @endguest
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- ══ Footer ══ --}}
    <footer class="bg-slate-950 text-slate-300">
        {{-- CTA band --}}
        <div class="border-b border-white/5 bg-gradient-to-r from-slate-900 to-slate-950">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                <div>
                    <h2 class="font-brownsugar text-3xl text-white">Ready to book your stay?</h2>
                    <p class="mt-1 text-slate-400">Message us on WhatsApp or call the front desk &mdash; we respond quickly.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="https://wa.me/{{ $whatsappDigits }}?text=Hi,%20I'd%20like%20to%20book%20a%20room." target="_blank"
                       class="whatsapp-link inline-flex items-center justify-center gap-2 rounded-full bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-3 shadow-lg transition duration-300">
                        <i class="fab fa-whatsapp text-lg"></i> Book on WhatsApp
                    </a>
                    <a href="{{ route('rooms') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-full bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-semibold px-6 py-3 transition duration-300">
                        <i class="fas fa-bed"></i> Browse Rooms
                    </a>
                </div>
            </div>
        </div>

        {{-- Main footer grid --}}
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="{{ route('home') }}" class="flex items-center text-2xl font-bold tracking-[0.15rem]" style="font-family: 'BrownSugar'">
                    @if (setting('logo'))
                        <img src="{{ asset('storage/' . setting('logo')) }}" alt="Brickspoint Hotel Logo" class="h-12 md:h-14 w-auto max-w-[220px] object-contain">
                    @else
                        Brickspoint <small class="font-gotham text-xs ml-1 text-slate-400">Wuse II</small>
                    @endif
                </a>
                <p class="mt-4 text-sm leading-relaxed text-slate-400">
                    Boutique aparthotel in the heart of Wuse II, Abuja. Serviced apartments with kitchenettes,
                    an in-house restaurant, lounge and concierge &mdash; your home away from home.
                </p>
                <div class="mt-5 flex items-center gap-3">
                    <a href="https://www.facebook.com/brickspointapartment" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="w-9 h-9 rounded-full bg-slate-900 hover:bg-amber-500 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white transition-colors"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/brickspapartment" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="w-9 h-9 rounded-full bg-slate-900 hover:bg-amber-500 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
                    <a href="https://twitter.com/bpaparthotel" target="_blank" rel="noopener noreferrer" aria-label="Twitter" class="w-9 h-9 rounded-full bg-slate-900 hover:bg-amber-500 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-white">Quick Links</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-amber-400 transition-colors">Home</a></li>
                    <li><a href="{{ route('rooms') }}" class="hover:text-amber-400 transition-colors">Rooms &amp; Suites</a></li>
                    <li><a href="{{ route('gallery') }}" class="hover:text-amber-400 transition-colors">Gallery</a></li>
                    <li><a href="{{ route('local-guide') }}" class="hover:text-amber-400 transition-colors">Explore Wuse II</a></li>
                    <li><a href="{{ route('menu') }}" class="hover:text-amber-400 transition-colors">Food Menu</a></li>
                    <li><a href="{{ route('favorites') }}" class="hover:text-amber-400 transition-colors">My Favorites</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-white">Contact</h3>
                <ul class="mt-5 space-y-4 text-sm text-slate-400">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-location-dot text-amber-500 mt-1"></i>
                        <span>11 Adzope Crescent, off Kumasi Crescent,<br>Wuse II, Abuja 900288</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-phone text-amber-500"></i>
                        <a href="tel:+2348099999620" class="hover:text-amber-400 transition-colors">+234 809 999 9620</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-amber-500"></i>
                        <a href="mailto:reservations@brickspoint.ng" class="hover:text-amber-400 transition-colors">reservations@brickspoint.ng</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fab fa-whatsapp text-amber-500"></i>
                        <a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer" class="hover:text-amber-400 transition-colors">WhatsApp Us</a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-white">Good To Know</h3>
                <ul class="mt-5 space-y-3 text-sm text-slate-400">
                    <li class="flex items-center justify-between gap-4 border-b border-white/5 pb-2"><span>Check-in</span><span class="text-white font-medium">2:00 PM</span></li>
                    <li class="flex items-center justify-between gap-4 border-b border-white/5 pb-2"><span>Check-out</span><span class="text-white font-medium">12:00 PM</span></li>
                    <li class="flex items-center justify-between gap-4 border-b border-white/5 pb-2"><span>Reception</span><span class="text-white font-medium">24 / 7</span></li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-mug-hot text-amber-500"></i>
                        <span>Complimentary breakfast</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-wifi text-amber-500"></i>
                        <span>Free hi-speed Wi-Fi</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-white/5">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} BRICKSPOINT BOUTIQUE APARTHOTEL. All Rights Reserved.</p>
                <p class="flex items-center gap-1.5"><i class="fas fa-location-dot text-amber-600"></i> Wuse II, Abuja &middot; <span class="text-slate-400">Proudly Nigerian</span></p>
            </div>
        </div>
    </footer>

    @include('layouts._whatsapp_modal_and_script')

    <script>
    (function () {
        // Mobile nav: close on X button or any link tap
        const menu     = document.getElementById('mobile-menu');
        const closeBtn = document.getElementById('mobile-menu-close');
        if (!menu) return;
        if (closeBtn) {
            closeBtn.addEventListener('click', () => menu.classList.add('hidden'));
        }
        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', () => menu.classList.add('hidden'));
        });

        // "Our Hotel" mobile accordion
        const hotelToggle   = document.getElementById('mobile-hotel-toggle');
        const hotelDropdown = document.getElementById('mobile-hotel-dropdown');
        const hotelChevron  = document.getElementById('mobile-hotel-chevron');
        if (hotelToggle && hotelDropdown) {
            hotelToggle.addEventListener('click', function () {
                const isOpen = !hotelDropdown.classList.contains('hidden');
                hotelDropdown.classList.toggle('hidden', isOpen);
                if (hotelChevron) hotelChevron.style.transform = isOpen ? '' : 'rotate(180deg)';
            });
        }
    })();

    // Scroll reveal
    (function () {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce) return;
        const els = document.querySelectorAll('.reveal');
        if (!('IntersectionObserver' in window)) {
            els.forEach(el => el.classList.add('is-visible'));
            return;
        }
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        els.forEach(el => io.observe(el));
    })();
    </script>

</body>

</html>