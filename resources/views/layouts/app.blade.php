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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --button-bg: #e87102;
            --button-bg-hover: #D97706;
            --button-text: #FFFFFF;
        }
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

        body {
            font-family: 'FuturaLT', 'BrownSugar';
            color: #000;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            font-size: 2.5rem;
            color: #d1d5db;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-rating input[type="radio"]:checked~label,
        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #f59e0b;
        }
    </style>

    @stack('styles')

    <!-- JSON-LD Structured Data for Google -->
    <script type="application/ld+json">
        @json($structuredData)
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    <header class="bg-gray-900/80 backdrop-blur text-white sticky top-0 z-40">
        <div class="container mx-auto flex justify-between items-center px-4 py-2 md:py-3">
            <a href="{{ route('home') }}" class="flex items-center text-xl md:text-3xl font-bold tracking-[0.15rem] md:tracking-[0.2rem]"
                style="font-family: 'BrownSugar'">
                @if (setting('logo'))
                <img src="{{ asset('storage/' . setting('logo')) }}" alt="Brickspoint Hotel Logo"
                        class="h-8 md:h-12 lg:h-16 w-auto max-w-[130px] md:max-w-xs object-contain">
                @else
                    Brickspoint <small class="font-gotham text-xs ml-1">Wuse II</small>
                @endif
            </a>
            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center space-x-3 lg:space-x-6 text-sm lg:text-base xl:text-xl">
                <a href="{{ route('home') }}" class="hover:text-amber-400 transition-colors whitespace-nowrap">Home</a>
                <a href="{{ route('rooms') }}" class="hover:text-amber-400 transition-colors whitespace-nowrap">Rooms</a>
                <a href="{{ route('gallery') }}" class="hover:text-amber-400 transition-colors whitespace-nowrap">Gallery</a>
                <a href="{{ route('local-guide') }}" class="hover:text-amber-400 transition-colors whitespace-nowrap">Explore Wuse II</a>
                <a href="{{ route('favorites') }}" class="hover:text-amber-400 transition-colors relative whitespace-nowrap">
                    Favorites
                    <span id="favorites-count"
                        class="absolute -top-2 -right-4 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                </a>
                <a href="{{ route('menu') }}" class="hover:text-amber-400 transition-colors whitespace-nowrap">Menu</a>
                <a href="{{ route('home') }}#contact" class="hover:text-amber-400 transition-colors whitespace-nowrap">Contact</a>
                @guest
                    <button type="button" id="feedback-link"
                        class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-3 lg:px-5 py-2 lg:py-3 rounded-md shadow-md transition duration-300 whitespace-nowrap">
                        <i class="fas fa-comment-dots text-white"></i>
                        <span class="font-medium hidden lg:inline">Leave Feedback</span>
                        <span class="font-medium lg:hidden">Feedback</span>
                    </button>
                @endguest
            </nav>
            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="p-2 text-white" aria-label="Open navigation menu">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-gray-900 border-t border-gray-700">
            {{-- Menu header with close button --}}
            <div class="flex justify-between items-center px-5 py-3 border-b border-gray-800">
                <span class="text-gray-400 text-xs font-semibold uppercase tracking-widest">Navigation</span>
                <button id="mobile-menu-close" aria-label="Close menu" class="text-gray-400 hover:text-white p-1 -mr-1">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <nav class="flex flex-col py-1">
                <a href="{{ route('home') }}" class="px-5 py-3.5 text-white hover:bg-gray-800 hover:text-amber-400 transition-colors text-base border-b border-gray-800/50">
                    <i class="fas fa-home w-5 mr-2 text-gray-500"></i>Home
                </a>
                <a href="{{ route('rooms') }}" class="px-5 py-3.5 text-white hover:bg-gray-800 hover:text-amber-400 transition-colors text-base border-b border-gray-800/50">
                    <i class="fas fa-bed w-5 mr-2 text-gray-500"></i>Rooms
                </a>
                <a href="{{ route('gallery') }}" class="px-5 py-3.5 text-white hover:bg-gray-800 hover:text-amber-400 transition-colors text-base border-b border-gray-800/50">
                    <i class="fas fa-images w-5 mr-2 text-gray-500"></i>Gallery
                </a>
                <a href="{{ route('local-guide') }}" class="px-5 py-3.5 text-white hover:bg-gray-800 hover:text-amber-400 transition-colors text-base border-b border-gray-800/50">
                    <i class="fas fa-map-marker-alt w-5 mr-2 text-gray-500"></i>Explore Wuse II
                </a>
                <a href="{{ route('favorites') }}" class="px-5 py-3.5 text-white hover:bg-gray-800 hover:text-amber-400 transition-colors text-base border-b border-gray-800/50">
                    <i class="fas fa-heart w-5 mr-2 text-gray-500"></i>My Favorites
                </a>
                <a href="{{ route('menu') }}" class="px-5 py-3.5 text-white hover:bg-gray-800 hover:text-amber-400 transition-colors text-base border-b border-gray-800/50">
                    <i class="fas fa-utensils w-5 mr-2 text-gray-500"></i>Food Menu
                </a>
                <a href="{{ route('home') }}#contact" class="px-5 py-3.5 text-white hover:bg-gray-800 hover:text-amber-400 transition-colors text-base">
                    <i class="fas fa-envelope w-5 mr-2 text-gray-500"></i>Contact
                </a>
                @guest
                <div class="px-5 py-4 border-t border-gray-800 mt-1">
                    <button type="button" id="mobile-feedback-link"
                        class="w-full inline-flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 rounded-md shadow-md transition duration-300">
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

    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto text-center">
            <p>&copy; {{ date('Y') }} BRICKSPOINT BOUTIQUE APARTHOTEL. All Rights Reserved.</p>
            <p class="text-white">&trade; Developed with ❤️ by IT Team</p>

        </div>
    </footer>

    @include('layouts._whatsapp_modal_and_script')

    <script>
    // Mobile nav: close on X button or any link tap
    (function () {
        const menu     = document.getElementById('mobile-menu');
        const closeBtn = document.getElementById('mobile-menu-close');
        if (!menu) return;
        if (closeBtn) {
            closeBtn.addEventListener('click', () => menu.classList.add('hidden'));
        }
        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', () => menu.classList.add('hidden'));
        });
    })();
    </script>

</body>

</html>
