@extends('layouts.app')

@section('title', 'Explore Wuse II - Local Guide')

@section('content')
<div class="relative bg-gray-800 py-32 px-6">
    <div class="absolute inset-0">
        <img src="https://placehold.co/1920x800/333333/FFFFFF?text=Explore+Wuse+II" alt="Map of Wuse II" class="h-full w-full object-cover">
        <div class="absolute inset-0 bg-gray-800 mix-blend-multiply"></div>
    </div>
    <div class="relative mx-auto max-w-7xl text-center">
        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">Explore Wuse II</h1>
        <p class="mt-6 max-w-3xl mx-auto text-xl text-indigo-100">Discover the best places to eat, shop, and visit, all curated by your hosts at Brickspoint.</p>
    </div>
</div>

<div class="bg-white py-16">
    <div class="container mx-auto px-6">
        @if($attractions->isEmpty())
            <p class="text-center text-gray-600 text-xl">Our local guide is currently being updated. Please check back soon!</p>
        @else
            <!-- Filters -->
            <div class="flex justify-center space-x-4 mb-12">
                <button class="filter-btn active px-4 py-2 rounded-full font-semibold" data-filter="*">All</button>
                @foreach($categories as $category)
                    <button class="filter-btn px-4 py-2 rounded-full font-semibold" data-filter=".{{ Str::slug($category) }}">{{ $category }}</button>
                @endforeach
            </div>

            <!-- Attractions Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 attraction-grid">
                @foreach($attractions as $attraction)
                    <div class="attraction-item {{ Str::slug($attraction->category) }} bg-gray-50 rounded-lg shadow-lg overflow-hidden">
                        <img src="{{ asset('storage/' . $attraction->image) }}" alt="{{ $attraction->name }}" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <h3 class="text-2xl font-bold mb-2">{{ $attraction->name }}</h3>
                            <span class="inline-block bg-green-200 text-green-800 text-xs px-2 rounded-full uppercase font-semibold tracking-wide">{{ $attraction->category }}</span>
                            <p class="text-gray-600 mt-4">{{ $attraction->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
.filter-btn {
    background-color: #f3f4f6;
    color: #4b5563;
    transition: all 0.2s ease-in-out;
}
.filter-btn.active, .filter-btn:hover {
    background-color: #10b981;
    color: white;
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

            attractionItems.forEach(item => {
                if (filter === '*' || item.classList.contains(filter.substring(1))) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

@endsection
