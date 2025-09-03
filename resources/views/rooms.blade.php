@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="relative bg-gray-800 py-32 px-6 sm:py-40 sm:px-8 lg:px-12">
    <div class="absolute inset-0">
        <img src="https://placehold.co/1920x800/333333/FFFFFF?text=Our+Comfortable+Rooms" alt="Header showing a luxurious hotel room" class="h-full w-full object-cover">
        <div class="absolute inset-0 bg-gray-800 mix-blend-multiply" aria-hidden="true"></div>
    </div>
    <div class="relative mx-auto max-w-7xl text-center">
        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">Our Rooms & Suites</h1>
        <p class="mt-6 max-w-3xl mx-auto text-xl text-indigo-100">Find the perfect space for your stay. Each room is designed with your comfort in mind.</p>
    </div>
</div>

{{-- Rooms Listing Section --}}
<div class="bg-white">
    <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 class="sr-only">Our Rooms</h2>

        @if($rooms->count() > 0)
            <div class="grid grid-cols-1 gap-y-10 gap-x-6 lg:grid-cols-2 xl:gap-x-8">
                 @php
                    $usd_rate = (float) setting('usd_exchange_rate', 0); // Get the rate from settings
                @endphp
                @foreach($rooms as $room)
                    <div class="group relative rounded-lg border border-gray-200 p-4 sm:p-6 flex flex-col">
                        <div class="aspect-w-3 aspect-h-2 overflow-hidden rounded-lg bg-gray-200 group-hover:opacity-75">
                             <!-- Favorite Button -->
                            <button onclick="toggleFavorite({{ $room->id }})" class="favorite-btn absolute top-4 right-4 bg-white/80 rounded-full p-2 z-10 transition-transform duration-200 hover:scale-110" data-room-id="{{ $room->id }}">
                                <i class="far fa-heart text-gray-700 text-xl"></i>
                            </button>
                            <a href="{{ route('rooms.show', $room) }}">
                                <img src="{{ asset('storage/' . $room->image) }}" alt="{{ $room->name }}" class="h-full w-full object-cover object-center">
                            </a>
                        </div>
                        <div class="pt-6 pb-4 text-center flex-grow">
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ $room->name }}
                            </h3>
                            <p class="mt-2 text-base text-gray-600">{{ $room->description }}</p>
                            <div class="mt-4 flex flex-wrap justify-center gap-x-4 gap-y-2 text-sm text-gray-500">
                                @if(isset($room->features) && is_array($room->features))
                                    @foreach($room->features as $feature)
                                        <span><i class="fas {{ $feature['icon'] }} mr-1"></i> {{ $feature['name'] }}</span>
                                    @endforeach
                                @endif
                                <span><i class="fas fa-users mr-1"></i> {{ $room->guests }} Guest(s)</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-center mt-auto">
                             <div class="text-gray-600 mb-4 text-center">
                                <p class="font-bold text-xl text-gray-900">From ₦{{ number_format($room->price, 2) }} / night</p>
                                @if($usd_rate > 0)
                                    <p class="text-sm">Approx. ${{ number_format($room->price / $usd_rate, 2) }}</p>
                                @endif
                            </div>
                            <a href="https://wa.me/{{ setting('whatsapp_number', '+2348099999620') }}?text=Hi,%20I'm%20interested%20in%20booking%20the%20{{ urlencode($room->name) }}." target="_blank" class="whatsapp-link mt-4 w-full max-w-xs bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 inline-flex items-center justify-center">
                                <i class="fab fa-whatsapp mr-2"></i> Reserve via WhatsApp
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-600 text-xl">We are currently updating our room listings. Please check back soon for our beautiful accommodations!</p>
        @endif
    </div>
</div>

@endsection
