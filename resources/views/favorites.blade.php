@extends('layouts.app')

@section('title', 'My Favorite Rooms')

@section('content')

{{-- Page Header --}}
<div class="relative bg-gray-800 py-32 px-6 sm:py-40 sm:px-8 lg:px-12">
    <div class="absolute inset-0">
        <img src="https://placehold.co/1920x800/444444/FFFFFF?text=My+Favorites" alt="Header showing a luxurious hotel room" class="h-full w-full object-cover">
        <div class="absolute inset-0 bg-gray-800 mix-blend-multiply" aria-hidden="true"></div>
    </div>
    <div class="relative mx-auto max-w-7xl text-center">
        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">My Favorite Rooms</h1>
        <p class="mt-6 max-w-3xl mx-auto text-xl text-indigo-100">Here are the rooms you've saved. Compare your top choices and get ready to book!</p>
    </div>
</div>

{{-- Favorite Rooms Listing Section --}}
<div class="bg-white">
    <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <div id="favorite-rooms-container" class="grid grid-cols-1 gap-y-10 gap-x-6 lg:grid-cols-2 xl:gap-x-8">
            <!-- Favorite rooms will be loaded here by JavaScript -->
        </div>

        <div id="no-favorites-message" class="hidden text-center text-gray-600">
            <i class="fas fa-heart-broken text-5xl text-gray-400 mb-4"></i>
            <h2 class="text-2xl font-bold mb-2">You haven't saved any rooms yet.</h2>
            <p class="mb-6">Click the heart icon on any room to add it to your favorites.</p>
            <a href="{{ route('rooms') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-lg transition duration-300">Explore Rooms</a>
        </div>

        <div id="loading-spinner" class="text-center text-gray-500">
            <i class="fas fa-spinner fa-spin text-4xl"></i>
            <p class="mt-2">Loading your favorites...</p>
        </div>
    </div>
</div>

@endsection
