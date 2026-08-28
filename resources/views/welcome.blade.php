@extends('layouts.app')

{{-- Add custom font imports in a new stack for this page --}}
@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- 'Ms Madi' is a great alternative for the script font 'BrownSugar' --}}
    {{-- 'Montserrat' is a great alternative for the geometric font 'Gotham' --}}
    <link href="https://fonts.googleapis.com/css2?family=Ms+Madi&family=Montserrat:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        .font-brownsugar {
            font-family: 'BrownSugar', cursive;
        }

        .font-gotham {
            font-family: 'GothamLight', 'FuturaLT';
        }
    </style>
@endpush
@section('content')

    {{-- Dynamic Hero Section --}}
    <section class="hero-section relative min-h-screen flex items-center justify-center text-white overflow-hidden -mt-20">
        @php
            $heroMedia = setting('hero_media');
        @endphp
        @if ($heroMedia && Str::endsWith($heroMedia, '.mp4'))
            <video autoplay loop muted playsinline class="absolute z-0 w-full h-full object-cover">
                <source src="{{ asset('storage/' . $heroMedia) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        @elseif($heroMedia)
            <div class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('{{ asset('storage/' . $heroMedia) }}');"></div>
        @else
            {{-- Fallback if no setting is found --}}
            <div class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('https://placehold.co/1920x1080/000000/FFFFFF?text=Stunning+Hotel+View');">
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/55 to-black/70"></div>

        <div class="relative z-10 text-center px-4 py-24 max-w-4xl mx-auto">
            <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/20 text-amber-300 text-xs md:text-sm font-gotham uppercase tracking-[0.3em] px-4 py-2 rounded-full mb-6">
                <i class="fas fa-star text-amber-400"></i> Boutique Aparthotel &bull; Wuse II, Abuja
            </span>

            <h1 class="font-brownsugar text-6xl md:text-8xl leading-none">
                <span class="block text-amber-400">{{ setting('hero_title', 'Welcome To') }}</span>
                <span class="block mt-2">Brickspoint</span>
            </h1>

            <p class="font-gotham text-lg md:text-2xl tracking-[0.35em] uppercase mt-4 text-gray-100">
                {{ setting('slogan', 'Your Home Away From Home...') }}</p>

            <p class="text-base md:text-xl font-light text-gray-200 mt-4 max-w-2xl mx-auto">
                {{ setting('hero_subtitle', 'No forms. No hassle. Just message us on WhatsApp to reserve your stay.') }}</p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-8">
                <a href="https://wa.me/{{ setting('whatsapp_number', '+2348099999620') }}?text=Hi,%20I'd%20like%20to%20book%20a%20room."
                    target="_blank"
                    class="whatsapp-link bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-8 rounded-full text-lg transition duration-300 ease-in-out transform hover:scale-105 inline-flex items-center gap-3 shadow-xl shadow-green-900/30">
                    <i class="fab fa-whatsapp text-2xl"></i> Book Now on WhatsApp
                </a>
                <a href="#rooms"
                    class="bg-white/10 hover:bg-white/20 backdrop-blur border border-white/25 text-white font-semibold py-4 px-8 rounded-full text-lg transition duration-300 ease-in-out inline-flex items-center gap-3">
                    <i class="fas fa-bed text-amber-300"></i> View Rooms &amp; Suites
                </a>
            </div>
        </div>

        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 animate-bounce text-gray-300">
            <i class="fas fa-chevron-down text-2xl"></i>
        </div>
    </section>

    <!-- Featured Rooms Section -->
    <section id="rooms" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="inline-flex items-center gap-2 text-amber-600 font-semibold uppercase tracking-[0.25em] text-sm mb-3">
                    <i class="fas fa-bed"></i> Stay with us
                </span>
                <h2 class="text-4xl font-bold">Featured Rooms &amp; Suites</h2>
                <p class="text-gray-600 mt-3">Discover our curated selection of rooms, each
                    designed for ultimate comfort and relaxation.</p>
            </div>

            @if ($featuredRoomTypes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @php
                        $usd_rate = (float) setting('usd_exchange_rate', 0); // Get the rate from settings
                    @endphp

                    @foreach ($featuredRoomTypes as $roomType)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden group relative border border-gray-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                            <!-- Favorite Button -->
                            <button onclick="toggleFavorite('roomtype-{{ $roomType->id }}')"
                                class="favorite-btn absolute top-4 right-4 bg-white/90 backdrop-blur rounded-full p-2.5 z-10 shadow transition-transform duration-200 hover:scale-110"
                                data-room-id="roomtype-{{ $roomType->id }}">
                                <i class="far fa-heart text-gray-700 text-xl"></i>
                            </button>
                            <a href="{{ route('rooms.show', $roomType) }}" class="block overflow-hidden">
                                <img src="{{ thumbnail_url($roomType->image, 'card') }}" alt="{{ $roomType->name }}"
                                    class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                            </a>
                            <div class="p-6">
                                <h3 class="text-2xl font-bold mb-2">{{ $roomType->name }}</h3>
                                <div class="flex items-end justify-between mb-4 pb-4 border-b border-gray-100">
                                    <div>
                                        <p class="font-bold text-2xl text-amber-600">₦{{ number_format($roomType->price, 2) }}</p>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">per night</p>
                                    </div>
                                    @if ($usd_rate > 0)
                                        <p class="text-sm text-gray-500">~ ${{ number_format($roomType->price / $usd_rate, 2) }}</p>
                                    @endif
                                </div>
                                <a href="https://wa.me/{{ setting('whatsapp_number', '+2348099999620') }}?text=Hi,%20I'm%20interested%20in%20the%20{{ urlencode($roomType->name) }}."
                                    target="_blank"
                                    class="whatsapp-link w-full inline-flex items-center justify-center gap-2 bg-gray-800 hover:bg-amber-600 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-300">
                                    <i class="fab fa-whatsapp"></i> Reserve via WhatsApp
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-12">
                    <a href="{{ route('rooms') }}"
                        class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3.5 px-9 rounded-lg transition duration-300 shadow-lg shadow-amber-500/30">
                        View All Rooms <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @else
                <p class="text-center text-gray-600">Our rooms are currently being prepared. Please check back soon!</p>
            @endif
        </div>
    </section>



    <!-- Why Choose Us Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 text-center">
            <div class="text-center mb-12">
                <span class="inline-flex items-center gap-2 text-amber-600 font-semibold uppercase tracking-[0.25em] text-sm mb-3"><i class="fas fa-gem"></i> Experience</span>
                <h2 class="text-4xl font-bold">Why Stay With Us?</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="flex flex-col items-center">
                    <div class="bg-amber-500 text-white rounded-full p-4 mb-4">
                        <i class="fas fa-concierge-bell text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold">24/7 Concierge</h3>
                    <p class="text-gray-600">Always here to help you.</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-amber-500 text-white rounded-full p-4 mb-4">
                        <i class="fas fa-map-marker-alt text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold">Prime Location</h3>
                    <p class="text-gray-600">In the heart of the city.</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-amber-500 text-white rounded-full p-4 mb-4">
                        <i class="fas fa-utensils text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold">Free Breakfast</h3>
                    <p class="text-gray-600">Start your day right.<small>T&c apply</small></p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-amber-500 text-white rounded-full p-4 mb-4">
                        <i class="fas fa-plane-arrival text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold">Airport Pickup</h3>
                    <p class="text-gray-600">Hassle-free travel.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <span class="inline-flex items-center gap-2 text-amber-600 font-semibold uppercase tracking-[0.25em] text-sm mb-3"><i class="fas fa-comments"></i> Reviews</span>
                <h2 class="text-4xl font-bold">What Our Guests Say</h2>
            </div>

            @if ($testimonials->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($testimonials as $testimonial)
                        <div class="bg-white border border-gray-100 rounded-xl shadow-lg p-7 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                            <i class="fas fa-quote-left text-amber-500/40 text-4xl mb-3"></i>
                            <div class="flex items-center gap-1 mb-4">
                                @for ($i = 0; $i < 5; $i++)
                                    @if ($i < $testimonial->rating)
                                        <i class="fas fa-star text-amber-400"></i>
                                    @else
                                        <i class="far fa-star text-gray-300"></i>
                                    @endif
                                @endfor
                            </div>
                            <p class="text-gray-600 italic mb-5">"{{ $testimonial->message }}"</p>
                            <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                                <p class="font-bold text-gray-800">- {{ $testimonial->name ?? 'Anonymous' }}</p>
                                <p class="text-xs text-gray-500">{{ $testimonial->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center mt-8 bg-amber-50 border border-amber-200 rounded-lg p-6 shadow-md">
                    <h3 class="text-2xl font-semibold text-amber-800 mb-2">Your voice matters!</h3>
                    <p class="text-gray-700 mb-4">Be the first to share your experience and inspire others to visit
                        BRICKSPOINT.</p>

                   <button type="button" id="feedback-link-welcome"
                         class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 rounded-md shadow-md transition duration-300">
                        <i class="fas fa-comment-dots text-white"></i>
                        <span class="font-medium">Leave Feedback</span>
                    </button>
                </div>
            @endif

        </div>
    </section>

    <!-- Contact & Location Section -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
            <div>
                <span class="inline-flex items-center gap-2 text-amber-600 font-semibold uppercase tracking-[0.25em] text-sm mb-3"><i class="fas fa-location-dot"></i> Find Us</span>
                <h2 class="text-4xl font-bold mb-6">Contact &amp; Location</h2>
                <p class="text-gray-600 mb-4">{{ $address }}</p>
                <p class="text-gray-600 mb-4">Email: {{ $email }}</p>
                <p class="text-gray-600 mb-8">Phone: {{ $phone }}</p>
                <a href="https://wa.me/+2348099999620?text=Hi,%20I%20have%20a%20question%20for%20Brickspoint%20Wuse."
                    target="_blank"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 inline-flex items-center mb-10">
                    <i class="fab fa-whatsapp mr-2"></i> Chat With Us on WhatsApp
                </a>

                <h3 class="text-3xl font-bold mb-4 mt-8">Or Send Us a Message</h3>

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Success</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                    @csrf
                    @include('partials._antibot')
                    <div>
                        <label for="name" class="block mb-1 font-medium">Full Name</label>
                        <input type="text" id="name" name="name"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition bg-white" required>
                    </div>
                    <div>
                        <label for="email" class="block mb-1 font-medium">Email Address</label>
                        <input type="email" id="email" name="email"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition bg-white" required>
                    </div>
                    <div>
                        <label for="message" class="block mb-1 font-medium">Message</label>
                        <textarea id="message" name="message" rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition bg-white"
                            required></textarea>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-7 rounded-lg transition duration-300 shadow-md shadow-amber-500/30">
                        Send Message <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
            <div>
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d986.1622694438759!2d7.4798507567178305!3d9.083578854700187!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x104e0af63a4024c7%3A0x5c173dd7f1e8dde4!2sBrickspoint!5e0!3m2!1sen!2sng!4v1756256795904!5m2!1sen!2sng"
                    width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" class="rounded-lg shadow-lg">
                </iframe>
            </div>
        </div>
    </section>


@endsection
