@extends('layouts.app')

@section('title', 'My Favorite Rooms')

@section('content')

    @include('partials._page_header', [
        'title' => 'My Favorite Rooms',
        'eyebrow' => 'Saved For Later',
        'icon' => 'fa-heart',
        'imageUrl' => null,
        'subtitle' => 'The rooms you\'ve saved, ready to compare and book whenever you are.',
    ])

    <div class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
            <div id="favorite-rooms-container" class="grid grid-cols-1 gap-8 lg:grid-cols-2 xl:gap-10">
                <!-- Favorite rooms are rendered here by JavaScript -->
            </div>

            <div id="no-favorites-message" class="hidden mx-auto max-w-xl py-10 text-center">
                <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-amber-50 text-4xl text-amber-400"><i class="fas fa-heart"></i></span>
                <h2 class="mt-6 text-2xl font-extrabold text-gray-900 sm:text-3xl">You haven't saved any rooms yet.</h2>
                <p class="mt-2 text-gray-600">Click the heart icon on any room to add it to your favorites and pick up where you left off.</p>
                <a href="{{ route('rooms') }}" class="mt-8 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 px-8 py-3.5 font-bold text-white shadow-glow transition hover:from-amber-600 hover:to-amber-700">
                    <i class="fas fa-bed"></i> Explore Rooms
                </a>
            </div>

            <div id="loading-spinner" class="py-16 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-4xl text-amber-500"></i>
                <p class="mt-3 font-semibold">Loading your favorites...</p>
            </div>
        </div>
    </div>

@endsection