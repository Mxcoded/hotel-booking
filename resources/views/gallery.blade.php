@extends('layouts.app')

@php
    $headerImg = $galleryImages->isNotEmpty() ? thumbnail_url($galleryImages->first()->path, 'card') : null;
@endphp

@section('content')

    @include('partials._page_header', [
        'title' => 'Our Gallery',
        'eyebrow' => 'Take A Look Around',
        'icon' => 'fa-camera',
        'imageUrl' => $headerImg,
        'subtitle' => 'A glimpse into the comfort, style, and luxury that awaits you at Brickspoint Hotel.',
    ])

    <section id="gallery" class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
            @if($galleryImages && $galleryImages->count() > 0)
                <div class="columns-2 gap-4 md:columns-3 md:gap-5 [column-fill:_balance]">
                    @foreach($galleryImages as $index => $image)
                        <button type="button"
                                class="gallery-item mb-4 block w-full overflow-hidden rounded-2xl bg-gray-100 shadow-sm transition-shadow duration-300 hover:shadow-lift md:mb-5"
                                data-index="{{ $index }}"
                                data-caption="{{ $image->alt_text ?: 'Brickspoint Hotel' }}"
                                aria-label="View image">
                            <img src="{{ thumbnail_url($image->path, 'card') }}"
                                 data-full="{{ asset('storage/' . $image->path) }}"
                                 alt="{{ $image->alt_text }}"
                                 loading="lazy"
                                 class="{{ $index % 3 === 0 ? 'aspect-[3/4]' : ($index % 3 === 1 ? 'aspect-square' : 'aspect-[4/3]') }} w-full object-cover transition-transform duration-700 hover:scale-105">
                        </button>
                    @endforeach
                </div>

                <p class="mt-8 text-center text-sm text-gray-400">
                    <i class="fas fa-image mr-1"></i> {{ $galleryImages->count() }} photo{{ $galleryImages->count() > 1 ? 's' : '' }} — Brickspoint Hotel, Wuse, Abuja
                </p>
            @else
                <div class="mx-auto max-w-xl text-center">
                    <i class="fa-regular fa-image text-6xl text-amber-300"></i>
                    <p class="mt-4 text-xl text-gray-600">No photos yet. Our gallery is coming soon — check back for a glimpse of the hotel!</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Lightbox --}}
    @if($galleryImages && $galleryImages->count() > 0)
    <div id="lightbox" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/90 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="Gallery image viewer">
        <button type="button" id="lb-close" class="absolute right-4 top-4 z-10 rounded-full bg-white/10 p-3 text-white transition hover:bg-white/25" aria-label="Close">
            <i class="fas fa-xmark text-xl"></i>
        </button>
        <button type="button" id="lb-prev" class="absolute left-3 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition hover:bg-white/25" aria-label="Previous image">
            <i class="fas fa-chevron-left text-xl"></i>
        </button>
        <button type="button" id="lb-next" class="absolute right-3 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition hover:bg-white/25" aria-label="Next image">
            <i class="fas fa-chevron-right text-xl"></i>
        </button>
        <figure class="max-h-[85vh] max-w-4xl">
            <img id="lb-img" src="" alt="" class="max-h-[78vh] w-auto rounded-lg object-contain shadow-2xl">
            <figcaption id="lb-caption" class="mt-3 text-center text-sm font-medium text-white/90"></figcaption>
        </figure>
    </div>

    <script>
    (function () {
        const items = Array.from(document.querySelectorAll('.gallery-item'));
        if (!items.length) return;

        const box = document.getElementById('lightbox');
        const lbImg = document.getElementById('lb-img');
        const lbCaption = document.getElementById('lb-caption');
        let current = 0;

        function show(i) {
            current = (i + items.length) % items.length;
            const img = items[current].querySelector('img');
            lbImg.src = img.dataset.full || img.src;
            lbImg.alt = img.alt;
            lbCaption.textContent = items[current].dataset.caption || '';
        }

        function open(i) {
            show(i);
            box.classList.remove('hidden');
            box.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function close() {
            box.classList.add('hidden');
            box.classList.remove('flex');
            document.body.style.overflow = '';
        }

        items.forEach((item, i) => item.addEventListener('click', () => open(i)));

        document.getElementById('lb-close').addEventListener('click', close);
        document.getElementById('lb-prev').addEventListener('click', () => show(current - 1));
        document.getElementById('lb-next').addEventListener('click', () => show(current + 1));

        document.addEventListener('keydown', function (e) {
            if (box.classList.contains('hidden')) return;
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowLeft') show(current - 1);
            if (e.key === 'ArrowRight') show(current + 1);
        });

        box.addEventListener('click', function (e) {
            if (e.target === box) close();
        });
    })();
    </script>
    @endif

@endsection