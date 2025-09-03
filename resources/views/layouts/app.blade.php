@php
    // Prepare the JSON-LD data as a PHP array
    $structuredData = [
      "@context" => "https://schema.org",
      "@type" => "Hotel",
      "name" => "Brickspoint Boutique Aparthotel",
      "address" => [
        "@type" => "PostalAddress",
        "streetAddress" => "11 Adzope Crescent",
        "addressLocality" => "Wuse II",
        "addressRegion" => "Abuja",
        "postalCode" => "900288",
        "addressCountry" => "NG"
      ],
      "image" => asset('storage/' . setting('logo')),
      "telephone" => setting('phone_number', '+2348099999620'),
      "url" => route('home'),
      "priceRange" => "₦173,250.00 - ₦500,000.00"
    ];
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

        body {
            font-family: 'Inter', sans-serif;
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
        <div class="container mx-auto flex justify-between items-center p-4">
             <a href="{{ route('home') }}" class="text-4xl font-bold font-brownsugar tracking-[0.4rem] leading-relaxed">
                @if (setting('logo'))
                    <img src="{{ asset('storage/' . setting('logo')) }}" alt="Brickspoint Hotel Logo"
                        class="h-16 w-auto">
                @else
                      Brickspoint <small class="font-gotham -mt-2 text-xs">Wuse II</small>
                @endif
            </a>
            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="hover:text-green-400 transition-colors">Home</a>
                <a href="{{ route('rooms') }}" class="hover:text-green-400 transition-colors">Rooms</a>
                <a href="{{ route('gallery') }}" class="hover:text-green-400 transition-colors">Gallery</a>
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
            <p>&copy; {{ date('Y') }} Brickspoint Hotel. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- WhatsApp Lead Capture Modal -->
    <div id="whatsapp-modal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-8 shadow-2xl max-w-sm w-full relative">
            <button id="close-modal-btn" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            <h3 class="text-2xl font-bold text-center mb-2">Chat on WhatsApp</h3>
            <p class="text-center text-gray-600 mb-6">Just enter your details below and we'll redirect you instantly.</p>
            <form id="whatsapp-lead-form">
                <div class="mb-4">
                    <label for="lead-name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="lead-name" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div class="mb-6">
                    <label for="lead-phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" id="lead-phone" name="phone" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center text-lg">
                    <i class="fab fa-whatsapp mr-2"></i> Chat Now
                </button>
            </form>
        </div>
    </div>

    <a href="https://wa.me/{{ setting('whatsapp_number', '+2348099999620') }}?text=Hi,%20I'd%20like%20to%20make%20an%20inquiry%20about%20your%20room%20and%20rates." target="_blank"
        class="whatsapp-link fixed bottom-8 right-8 bg-green-500 hover:bg-green-600 text-white rounded-full p-4 shadow-lg transition duration-300 ease-in-out transform hover:scale-110 z-50">
        <i class="fab fa-whatsapp text-4xl"></i>
    </a>

    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('whatsapp-modal');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const leadForm = document.getElementById('whatsapp-lead-form');
            const whatsappLinks = document.querySelectorAll('.whatsapp-link');
            
            let targetUrl = '';

            whatsappLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    targetUrl = this.href;
                    
                    // Check if user details are already in local storage
                    const leadDetails = localStorage.getItem('whatsappLead');
                    if (leadDetails) {
                        window.open(targetUrl, '_blank'); // Redirect directly
                    } else {
                        modal.classList.remove('hidden'); // Show the form
                    }
                });
            });

            closeModalBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
            });

            leadForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                const leadData = {
                    name: formData.get('name'),
                    phone: formData.get('phone')
                };

                fetch("{{ route('whatsapp.lead.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(leadData)
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Save details to local storage to remember the user
                        localStorage.setItem('whatsappLead', JSON.stringify(leadData));
                        
                        window.open(targetUrl, '_blank');
                        modal.classList.add('hidden');
                        this.reset();
                    } else {
                        alert('Something went wrong. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please check the console.');
                });
            });
        });
    </script>

</body>

</html>