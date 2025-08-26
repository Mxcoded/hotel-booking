@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="relative bg-gray-800 py-32 px-6 sm:py-40 sm:px-8 lg:px-12">
    <div class="absolute inset-0">
        <img src="https://placehold.co/1920x800/444444/FFFFFF?text=Hotel+Gallery" alt="Header showing a collage of hotel images" class="h-full w-full object-cover">
        <div class="absolute inset-0 bg-gray-800 mix-blend-multiply" aria-hidden="true"></div>
    </div>
    <div class="relative mx-auto max-w-7xl text-center">
        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">Our Gallery</h1>
        <p class="mt-6 max-w-3xl mx-auto text-xl text-indigo-100">A glimpse into the comfort and luxury that awaits you at Brickspoint Hotel.</p>
    </div>
</div>

<!-- Dynamic Gallery Section -->
<section id="gallery" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        @if($galleryImages && $galleryImages->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($galleryImages as $image)
                    <div class="overflow-hidden rounded-lg shadow-lg">
                        <img class="h-auto w-full max-w-full transform transition-transform duration-300 hover:scale-105" src="{{ asset('storage/' . $image->path) }}" alt="{{ $image->alt_text }}">
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-600 text-xl">No images have been uploaded yet. Our gallery is coming soon!</p>
        @endif
    </div>
</section>
@endsection
