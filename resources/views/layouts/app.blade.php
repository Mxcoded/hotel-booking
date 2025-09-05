@php
    // Prepare the JSON-LD data as a PHP array
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'Hotel',
        'name' => 'Brickspoint Boutique Aparthotel',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => '11 Adzope Crescent',
            'addressLocality' => 'Wuse II',
            'addressRegion' => 'Abuja',
            'postalCode' => '900288',
            'addressCountry' => 'NG',
        ],
        'image' => asset('storage/' . setting('logo')),
        'telephone' => setting('phone_number', '+2348099999620'),
        'url' => route('home'),
        'priceRange' => '₦173,250.00 - ₦500,000.00',
    ];
@endphp
@php
    $unreadContacts = \App\Models\Contact::where('is_read', false)->count();
    $unreadFeedback = \App\Models\Feedback::where('is_read', false)->count();
    $unreadFeedbackCount = $unreadFeedback;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('favicon')) }}">
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Brickspoint Boutique Aparthotel - Wuse II, Abuja')</title>
    <meta name="description"
        content="Book your stay at Brickspoint Boutique Aparthotel, a luxury hotel located at 11 Adzope Crescent, Wuse II, Abuja. Enjoy premium comfort and convenience.">
    <meta name="keywords"
        content="hotel in abuja, brickspoint, boutique hotel, aparthotel wuse 2, luxury hotel abuja, book hotel abuja">
    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Brickspoint Boutique Aparthotel - Wuse II, Abuja">
    <meta property="og:description"
        content="Luxury and comfort at 11 Adzope Crescent, Wuse II, Abuja. Book your stay directly via WhatsApp for the best experience.">
    <meta property="og:image" content="{{ asset('storage/' . setting('favicon')) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Brickspoint Boutique Aparthotel - Wuse II, Abuja">
    <meta property="twitter:description"
        content="Luxury and comfort at 11 Adzope Crescent, Wuse II, Abuja. Book your stay directly via WhatsApp for the best experience.">
    <meta property="twitter:image" content="{{ asset('storage/' . setting('favicon')) }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
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

        body {
            font-family: 'FuturaLT', 'BrownSugar';
            color: #000;
        }
        .star-rating input[type="radio"] { display: none; }
        .star-rating label { font-size: 2.5rem; color: #d1d5db; cursor: pointer; transition: color 0.2s; }
        .star-rating input[type="radio"]:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label { color: #f59e0b; }
    </style>

    @stack('styles')

    <!-- JSON-LD Structured Data for Google -->
    <script type="application/ld+json">
        @json($structuredData)
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    <header class="bg-gray-900/80 backdrop-blur text-white sticky top-0 z-40">
        <div class="container mx-auto flex justify-between items-center p-4">
            <a href="{{ route('home') }}" class="text-4xl font-bold font-brownsugar tracking-[0.2rem] leading-relaxed"
                style="font-family: 'BrownSugar'">
                @if (setting('logo'))
                    <img src="{{ asset('storage/' . setting('logo')) }}" alt="Brickspoint Hotel Logo"
                        class="h-16 w-auto">
                @else
                    Brickspoint <small class="font-gotham -mt-2 text-xs">Wuse II</small>
                @endif
            </a>
            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center space-x-6 text-xl">
                <a href="{{ route('home') }}" class="hover:text-green-400 transition-colors">Home</a>
                <a href="{{ route('rooms') }}" class="hover:text-green-400 transition-colors">Rooms</a>
                <a href="{{ route('gallery') }}" class="hover:text-green-400 transition-colors">Gallery</a>
                 <a href="{{ route('local-guide') }}" class="hover:text-green-400 transition-colors">Explore Wuse II</a>
                <a href="{{ route('favorites') }}" class="hover:text-green-400 transition-colors relative">
                    Favorites
                    <span id="favorites-count"
                        class="absolute -top-2 -right-4 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                </a>
                <a href="#" id="feedback-link" class="hover:text-green-400 transition-colors">Feedback</a>
                <a href="{{ route('home') }}#contact" class="hover:text-green-400 transition-colors">Contact</a>
                @guest
                    <a href="{{ route('login') }}" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-md">Login</a>
                @endguest
            </nav>
            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button id="mobile-menu-button">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-gray-800">
            <nav class="flex flex-col items-center space-y-4 py-4">
                <a href="{{ route('home') }}" class="hover:text-green-400 transition-colors">Home</a>
                <a href="{{ route('rooms') }}" class="hover:text-green-400 transition-colors">Rooms</a>
                <a href="{{ route('gallery') }}" class="hover:text-green-400 transition-colors">Gallery</a>
                <a href="{{ route('favorites') }}" class="hover:text-green-400 transition-colors">Favorites</a>
                <a href="#" id="mobile-feedback-link" class="hover:text-green-400 transition-colors">Feedback</a>
                <a href="{{ route('home') }}#contact" class="hover:text-green-400 transition-colors">Contact</a>
                @guest
                    <a href="{{ route('login') }}" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-md">Login</a>
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

</body>

</html>
