@extends('layouts.app')

{{-- Add custom font imports in a new stack for this page --}}
@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
{{-- 'Ms Madi' is a great alternative for the script font 'BrownSugar' --}}
{{-- 'Montserrat' is a great alternative for the geometric font 'Gotham' --}}
<link href="https://fonts.googleapis.com/css2?family=Ms+Madi&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
    .font-brownsugar {
        font-family: 'BrownSugar', cursive;
    }
    .font-gotham {
        font-family: 'GothamLight', sans-serif;
    }
</style>
@endpush
@section('content')

    {{-- Dynamic Hero Section --}}
    <section class="hero-section h-screen flex items-center justify-center text-white relative -mt-20">
        @php
            $heroMedia = setting('hero_media');
        @endphp
        @if($heroMedia && Str::endsWith($heroMedia, '.mp4'))
            <video autoplay loop muted playsinline class="absolute z-0 w-full h-full object-cover">
                <source src="{{ asset('storage/' . $heroMedia) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        @elseif($heroMedia)
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $heroMedia) }}');"></div>
        @else
            {{-- Fallback if no setting is found --}}
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://placehold.co/1920x1080/000000/FFFFFF?text=Stunning+Hotel+View');"></div>
        @endif

       <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="text-center z-10 p-4">
            <h1 class="text-5xl md:text-7xl font-extrabold leading-tight mb-4 font-brownsugar">{{ setting('hero_title', 'Welcome To') }}</h1>
            
        <!-- Updated Hero Content with Branded Fonts -->
        <div class="text-center z-10 p-4 flex flex-col items-center">
            <h1 class="font-brownsugar text-8xl md:text-9xl text-gray-380 -mb-4">B</h1>
            <h2 class="font-brownsugar text-6xl md:text-7xl">Brickspoint</h2>
            <p class="font-gotham text-base md:text-lg tracking-widest uppercase mt-2">Boutique Aparthotel</p>
            <p class="font-gotham text-base md:text-lg tracking-widest uppercase">Wuse II</p>
        </div>
        <p class="text-lg md:text-2xl mb-8 font-gotham">{{ setting('slogan', 'Your Home Away From Home...') }}</p>
        <p class="text-lg md:text-2xl mb-8 font-light">{{ setting('hero_subtitle', 'No forms. No hassle. Just message us on WhatsApp to reserve your stay.') }}</p>
            <a href="https://wa.me/{{ setting('whatsapp_number', '+2348099999620') }}?text=Hi,%20I'd%20like%20to%20book%20a%20room%20at%20Brickspoint." target="_blank" class="bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-8 rounded-full text-lg transition duration-300 ease-in-out transform hover:scale-105 inline-flex items-center">
                <i class="fab fa-whatsapp mr-3 text-2xl"></i> Book Now on WhatsApp
            </a>
        </div>
    </section>

    <!-- Featured Rooms Section -->
    <section id="rooms" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center mb-4">Featured Rooms & Suites</h2>
            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">Discover our curated selection of rooms, each designed for ultimate comfort and relaxation.</p>
            
            @if($featuredRooms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @php
                        $usd_rate = (float) setting('usd_exchange_rate', 0); // Get the rate from settings
                    @endphp
                    
                    @foreach($featuredRooms as $room)
                        <div class="bg-gray-50 rounded-lg shadow-lg overflow-hidden">
                            <a href="{{ route('rooms.show', $room) }}">
                                <img src="{{ asset('storage/' . $room->image) }}" alt="{{ $room->name }}" class="w-full h-64 object-cover">
                            </a>
                            <div class="p-6">
                                <h3 class="text-2xl font-bold mb-2">{{ $room->name }}</h3>
                                <div class="text-gray-600 mb-4">
                                    <p class="font-bold text-xl text-gray-900">From ₦{{ number_format($room->price, 2) }} / night</p>
                                    @if($usd_rate > 0)
                                        <p class="text-sm">Approx. ${{ number_format($room->price / $usd_rate, 2) }}</p>
                                    @endif
                                </div>
                                <a href="https://wa.me/{{ setting('whatsapp_number', '+2348099999620') }}?text=Hi,%20I'm%20interested%20in%20the%20{{ urlencode($room->name) }}." target="_blank" class="bg-gray-800 hover:bg-black text-white font-semibold py-2 px-4 rounded-lg w-full flex items-center justify-center">
                                    <i class="fab fa-whatsapp mr-2"></i> Reserve via WhatsApp
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-12">
                    <a href="{{ route('rooms') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-lg transition duration-300">View All Rooms</a>
                </div>
            @else
                 <p class="text-center text-gray-600">Our rooms are currently being prepared. Please check back soon!</p>
            @endif
        </div>
    </section>



   <!-- Why Choose Us Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-12">Why Stay With Us?</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="flex flex-col items-center">
                    <div class="bg-green-500 text-white rounded-full p-4 mb-4">
                        <i class="fas fa-concierge-bell text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold">24/7 Concierge</h3>
                    <p class="text-gray-600">Always here to help you.</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-green-500 text-white rounded-full p-4 mb-4">
                        <i class="fas fa-map-marker-alt text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold">Prime Location</h3>
                    <p class="text-gray-600">In the heart of the city.</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-green-500 text-white rounded-full p-4 mb-4">
                        <i class="fas fa-utensils text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold">Free Breakfast</h3>
                    <p class="text-gray-600">Start your day right.<small>T&c apply</small></p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-green-500 text-white rounded-full p-4 mb-4">
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
            <h2 class="text-4xl font-bold text-center mb-12">What Our Guests Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="p-6 rounded-lg">
                    <div class="whatsapp-bubble p-4 rounded-lg relative">
                        <p class="text-gray-700">"Booking through WhatsApp was a breeze! The staff was incredibly responsive and helpful. The hotel itself is beautiful. Highly recommended!"</p>
                        <div class="absolute bottom-0 right-0 transform translate-x-1/2 translate-y-1/2 rotate-45 w-4 h-4 bg-green-200"></div>
                    </div>
                    <p class="mt-4 font-bold text-right">- Sarah L.</p>
                </div>
                <!-- Testimonial 2 -->
                <div class="p-6 rounded-lg">
                    <div class="whatsapp-bubble p-4 rounded-lg relative">
                        <p class="text-gray-700">"The best hotel experience I've had in a long time. The location is perfect, and the service is top-notch. I'll definitely be back."</p>
                        <div class="absolute bottom-0 right-0 transform translate-x-1/2 translate-y-1/2 rotate-45 w-4 h-4 bg-green-200"></div>
                    </div>
                    <p class="mt-4 font-bold text-right">- Michael B.</p>
                </div>
                <!-- Testimonial 3 -->
                <div class="p-6 rounded-lg">
                    <div class="whatsapp-bubble p-4 rounded-lg relative">
                        <p class="text-gray-700">"A true 5-star experience from start to finish. Chatting with the concierge on WhatsApp for recommendations was so convenient."</p>
                        <div class="absolute bottom-0 right-0 transform translate-x-1/2 translate-y-1/2 rotate-45 w-4 h-4 bg-green-200"></div>
                    </div>
                    <p class="mt-4 font-bold text-right">- Emily R.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact & Location Section -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
            <div>
                <h2 class="text-4xl font-bold mb-6">Contact & Location</h2>
                <p class="text-gray-600 mb-4">{{ $address }}</p>
                <p class="text-gray-600 mb-4">Email: {{ $email }}</p>
                <p class="text-gray-600 mb-8">Phone: {{ $phone }}</p>
                <a href="https://wa.me/+2348099999620?text=Hi,%20I%20have%20a%20question%20for%20Brickspoint%20Wuse." target="_blank" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 inline-flex items-center mb-10">
                    <i class="fab fa-whatsapp mr-2"></i> Chat With Us on WhatsApp
                </a>

                <h3 class="text-3xl font-bold mb-4 mt-8">Or Send Us a Message</h3>

                {{-- Success Message --}}
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Success</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block mb-1 font-medium">Full Name</label>
                        <input type="text" id="name" name="name" class="w-full p-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label for="email" class="block mb-1 font-medium">Email Address</label>
                        <input type="email" id="email" name="email" class="w-full p-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label for="message" class="block mb-1 font-medium">Message</label>
                        <textarea id="message" name="message" rows="4" class="w-full p-2 border border-gray-300 rounded-lg" required></textarea>
                    </div>
                    <button type="submit" class="bg-gray-800 hover:bg-black text-white font-bold py-3 px-6 rounded-lg transition duration-300">Send Message</button>
                </form>
            </div>
            <div>
                <iframe 
                     src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d986.1622694438759!2d7.4798507567178305!3d9.083578854700187!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x104e0af63a4024c7%3A0x5c173dd7f1e8dde4!2sBrickspoint!5e0!3m2!1sen!2sng!4v1756256795904!5m2!1sen!2sng" 
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    class="rounded-lg shadow-lg">
                </iframe>
            </div>
        </div>
    </section>


@endsection
