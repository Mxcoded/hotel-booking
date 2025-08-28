<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <meta property="og:image" content="{{ asset('storage/' . setting('logo')) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Brickspoint Boutique Aparthotel - Wuse II, Abuja">
    <meta property="twitter:description"
        content="Luxury and comfort at 11 Adzope Crescent, Wuse II, Abuja. Book your stay directly via WhatsApp for the best experience.">
    <meta property="twitter:image" content="{{ asset('storage/' . setting('logo')) }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>


    <!-- JSON-LD Structured Data for Google -->
    @php
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Hotel',
            'name' => 'Brickspoint Boutique Aparthotel Wuse II',
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
            'priceRange' => '₦173,250.00 - ₦550,000.00 per night',
        ];
    @endphp
    <script type="application/ld+json">
        @json($structuredData)
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    <header class="bg-gray-900/80 backdrop-blur text-white sticky top-0 z-40">
        <div class="container mx-auto flex justify-between items-center p-4">
            <a href="{{ route('home') }}" class="text-3xl font-bold">
                @if (setting('logo'))
                    <img src="{{ asset('storage/' . setting('logo')) }}" alt="Brickspoint Hotel Logo"
                        class="h-12 w-auto">
                @else
                    Brickspoint
                @endif
            </a>
            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="hover:text-green-400 transition-colors">Home</a>
                <a href="{{ route('rooms') }}" class="hover:text-green-400 transition-colors">Rooms</a>
                <a href="{{ route('gallery') }}" class="hover:text-green-400 transition-colors">Gallery</a>
                <a href="{{ route('home') }}#contact" class="hover:text-green-400 transition-colors">Contact</a>
                {{-- @guest
                    <a href="{{ route('login') }}" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-md">Login</a>
                @endguest --}}
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
                <a href="{{ route('home') }}#contact" class="hover:text-green-400 transition-colors">Contact</a>
                {{-- @guest
                    <a href="{{ route('login') }}" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-md">Login</a>
                @endguest --}}
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto text-center">
            <p>&copy; {{ date('Y') }} Brickspoint Hotel. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Sticky WhatsApp Button -->
    <a href="https://wa.me/+2348099999620?text=Hi,%20I'd%20like%20to%20book%20a%20room%20at%20Brickspoint."
        target="_blank"
        class="fixed bottom-8 right-8 bg-green-500 hover:bg-green-600 text-white rounded-full p-4 shadow-lg transition duration-300 ease-in-out transform hover:scale-110 z-50">
        <i class="fab fa-whatsapp text-4xl"></i>
    </a>

    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

</body>

</html>
