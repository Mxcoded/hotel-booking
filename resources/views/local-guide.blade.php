@extends('layouts.app')

@section('title', 'Explore Wuse II - Local Guide')

@php
    $headerAttraction = $attractions->first(fn ($a) => !empty($a->image));
@endphp

@section('content')

    @include('partials._page_header', [
        'title' => 'Explore Wuse II',
        'eyebrow' => 'Local Guide',
        'icon' => 'fa-map-location-dot',
        'imageUrl' => $headerAttraction ? thumbnail_url($headerAttraction->image, 'card') : null,
        'subtitle' => 'Discover the best places to eat, shop, and visit around Wuse, Abuja — curated by your hosts at Brickspoint Hotel.',
    ])

    <div class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
            @if($attractions->isEmpty())
                <div class="mx-auto max-w-xl text-center">
                    <i class="fas fa-map-pin text-6xl text-amber-300"></i>
                    <p class="mt-4 text-xl text-gray-600">Our local guide is currently being updated. Please check back soon!</p>
                </div>
            @else
                {{-- Filters --}}
                <div class="mb-12 flex flex-wrap items-center justify-center gap-2">
                    <button class="filter-btn active rounded-full border border-amber-300 bg-amber-500 px-5 py-2.5 font-semibold text-white shadow-md shadow-amber-500/30 transition hover:bg-amber-600" data-filter="*">
                        All
                        <span class="ml-1 rounded-full bg-white/25 px-2 py-0.5 text-xs">{{ $attractions->count() }}</span>
                    </button>
                    @foreach($categories as $category)
                        <button class="filter-btn rounded-full border border-gray-200 bg-white px-5 py-2.5 font-semibold text-gray-600 transition hover:border-amber-300 hover:text-amber-700" data-filter=".{{ Str::slug($category) }}">
                            {{ $category }}
                            <span class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500">{{ $attractions->where('category', $category)->count() }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Attractions Grid --}}
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3 attraction-grid">
                    @foreach($attractions as $attraction)
                        <div class="attraction-item {{ Str::slug($attraction->category) }} reveal group overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:shadow-lift">
                            <div class="relative aspect-[4/3] overflow-hidden">
                                @if($attraction->image)
                                    <img src="{{ thumbnail_url($attraction->image, 'card') }}" alt="{{ $attraction->name }}" loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gray-100">
                                        <i class="fas fa-map-location-dot text-4xl text-gray-300"></i>
                                    </div>
                                @endif
                                <span class="absolute left-4 top-4 rounded-full bg-white/95 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-700 shadow">
                                    {{ $attraction->category }}
                                </span>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-extrabold text-gray-900 group-hover:text-amber-600">{{ $attraction->name }}</h3>
                                <p class="mt-3 text-gray-600">{{ $attraction->description }}</p>
                                @if(!empty($attraction->address))
                                    <p class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                                        <i class="fas fa-location-dot text-amber-500"></i> {{ $attraction->address }}
                                    </p>
                                @endif
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($attraction->name . ' Abuja') }}" target="_blank" rel="noopener noreferrer"
                                   class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-amber-600 transition hover:text-amber-700">
                                    Open in Maps <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(!empty(setting('whatsapp_number')))
                    <div class="mt-16 rounded-3xl bg-gradient-to-r from-amber-500 to-amber-600 p-8 text-center shadow-glow sm:p-10">
                        <h2 class="text-2xl font-extrabold text-white sm:text-3xl">Need recommendations?</h2>
                        <p class="mx-auto mt-2 max-w-xl text-amber-50">Ask our front desk for trusted nearby restaurants, markets, and attractions tailored to your plans.</p>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('whatsapp_number')) }}?text=Hi!%20Could%20you%20recommend%20places%20around%20Brickspoint%20Hotel?" target="_blank"
                           class="whatsapp-link mt-6 inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 font-bold text-amber-700 transition hover:bg-amber-50">
                            <i class="fab fa-whatsapp text-xl"></i> Chat with us
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <style>
        .filter-btn.active,
        .filter-btn.active:hover {
            background-color: #f59e0b;
            border-color: #f59e0b;
            color: #fff;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const attractionItems = document.querySelectorAll('.attraction-item');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');

                attractionItems.forEach((item, i) => {
                    if (filter === '*' || item.classList.contains(filter.substring(1))) {
                        item.style.display = '';
                        item.style.animation = 'none';
                        item.offsetHeight;
                        item.style.animation = 'fadeUp .45s ease both';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    });
    </script>

@endsection