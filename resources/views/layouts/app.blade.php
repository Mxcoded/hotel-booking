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
            // Smart Modal Logic
            const modal = document.getElementById('whatsapp-modal');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const leadForm = document.getElementById('whatsapp-lead-form');
            const whatsappLinks = document.querySelectorAll('.whatsapp-link');
            let targetUrl = '';
            whatsappLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    targetUrl = this.href;
                    if (localStorage.getItem('whatsappLead')) {
                        window.open(targetUrl, '_blank');
                    } else {
                        modal.classList.remove('hidden');
                    }
                });
            });
            if(closeModalBtn) {
                closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
            }
            if(leadForm) {
                leadForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const formData = new FormData(this);
                    const leadData = { name: formData.get('name'), phone: formData.get('phone') };
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
                            localStorage.setItem('whatsappLead', JSON.stringify(leadData));
                            window.open(targetUrl, '_blank');
                            modal.classList.add('hidden');
                            this.reset();
                        } else { alert('Something went wrong.'); }
                    })
                    .catch(error => console.error('Error:', error));
                });
            }

            // Favorites Logic
            const favorites = {
                get() {
                    return JSON.parse(localStorage.getItem('favoriteRooms') || '[]');
                },
                set(ids) {
                    localStorage.setItem('favoriteRooms', JSON.stringify(ids));
                    this.updateCount();
                },
                add(id) {
                    let ids = this.get();
                    if (!ids.includes(id)) {
                        ids.push(id);
                        this.set(ids);
                    }
                },
                remove(id) {
                    let ids = this.get().filter(i => i !== id);
                    this.set(ids);
                },
                toggle(id) {
                    let ids = this.get();
                    if (ids.includes(id)) {
                        this.remove(id);
                    } else {
                        this.add(id);
                    }
                    this.updateHeart(id);
                },
                updateCount() {
                    const count = this.get().length;
                    const countEl = document.getElementById('favorites-count');
                    if(countEl) {
                        countEl.textContent = count;
                        countEl.classList.toggle('hidden', count === 0);
                    }
                },
                updateHeart(roomId) {
                    const heart = document.querySelector(`.favorite-btn[data-room-id="${roomId}"] i`);
                    if(heart) {
                        const isFavorited = this.get().includes(roomId);
                        heart.classList.toggle('fas', isFavorited); // Solid icon
                        heart.classList.toggle('far', !isFavorited); // Outline icon
                        heart.classList.toggle('text-red-500', isFavorited);
                    }
                },
                initHearts() {
                    document.querySelectorAll('.favorite-btn').forEach(btn => {
                        const roomId = parseInt(btn.dataset.roomId);
                        this.updateHeart(roomId);
                    });
                }
            };

            favorites.updateCount();
            favorites.initHearts();

            window.toggleFavorite = (roomId) => {
                favorites.toggle(roomId);
            };

            // Logic for the favorites page
            if(document.getElementById('favorite-rooms-container')) {
                const container = document.getElementById('favorite-rooms-container');
                const noFavsMsg = document.getElementById('no-favorites-message');
                const spinner = document.getElementById('loading-spinner');
                const favoriteIds = favorites.get();

                if(favoriteIds.length > 0) {
                    fetch("{{ route('api.favorites') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ ids: favoriteIds })
                    })
                    .then(response => response.json())
                    .then(rooms => {
                        spinner.classList.add('hidden');
                        if(rooms.length > 0) {
                            container.innerHTML = rooms.map(room => {
                                const usd_rate = {{ (float) setting('usd_exchange_rate', 0) }};
                                const priceNaira = new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(room.price).replace('NGN', '₦');
                                let priceUsd = '';
                                if(usd_rate > 0) {
                                    priceUsd = `<p class="text-sm">Approx. $${(room.price / usd_rate).toFixed(2)}</p>`;
                                }

                                return `
                                    <div class="bg-gray-50 rounded-lg shadow-lg overflow-hidden relative">
                                        <button onclick="toggleFavorite(${room.id})" class="favorite-btn absolute top-4 right-4 bg-white/80 rounded-full p-2 z-10" data-room-id="${room.id}">
                                            <i class="fas fa-heart text-red-500 text-xl"></i>
                                        </button>
                                        <a href="/rooms/${room.id}">
                                            <img src="/storage/${room.image}" alt="${room.name}" class="w-full h-64 object-cover">
                                        </a>
                                        <div class="p-6">
                                            <h3 class="text-2xl font-bold mb-2">${room.name}</h3>
                                            <div class="text-gray-600 mb-4">
                                                <p class="font-bold text-xl text-gray-900">From ${priceNaira} / night</p>
                                                ${priceUsd}
                                            </div>
                                            <a href="https://wa.me/{{ setting('whatsapp_number', '+2348099999620') }}?text=Hi,%20I'm%20interested%20in%20the%20${encodeURIComponent(room.name)}." target="_blank" class="whatsapp-link bg-gray-800 hover:bg-black text-white font-semibold py-2 px-4 rounded-lg w-full flex items-center justify-center">
                                                <i class="fab fa-whatsapp mr-2"></i> Reserve via WhatsApp
                                            </a>
                                        </div>
                                    </div>
                                `;
                            }).join('');
                        } else {
                            noFavsMsg.classList.remove('hidden');
                        }
                    });
                } else {
                    spinner.classList.add('hidden');
                    noFavsMsg.classList.remove('hidden');
                }
            }
        });
    </script>

</body>

</html>