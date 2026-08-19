@extends('layouts.app')

@section('title', $roomType->name . ' - Brickspoint Hotel')

@section('content')
<div class="bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            {{-- Media Gallery Section --}}
            <div>
                {{-- Main Media Display --}}
                <div id="main-media-display" class="mb-4 rounded-lg overflow-hidden shadow-lg aspect-w-16 aspect-h-9 bg-gray-200">
                    @php
                        $mainMedia = $roomType->media()->where('type', 'image')->first();
                    @endphp
                    @if($mainMedia)
                        <img id="main-image" src="{{ asset('storage/' . $mainMedia->file_path) }}" alt="{{ $roomType->name }}" class="w-full h-full object-cover">
                    @else
                        <img id="main-image" src="https://placehold.co/800x533/333333/FFFFFF?text={{ urlencode($roomType->name) }}" alt="{{ $roomType->name }}" class="w-full h-full object-cover">
                    @endif
                </div>

                {{-- Thumbnails --}}
                <div class="grid grid-cols-4 sm:grid-cols-5 gap-2 mt-4">
                    @php
                        $allMedia = $roomType->media;
                    @endphp
                    @foreach($allMedia as $index => $media)
                        <div class="relative group cursor-pointer border-2 border-transparent hover:border-amber-500 rounded p-1 thumbnail-item">
                            @if ($media->type === 'image')
                                <img src="{{ asset('storage/' . $media->file_path) }}" alt="Room media thumbnail" class="w-full h-20 object-cover rounded" data-type="image" data-src="{{ asset('storage/' . $media->file_path) }}">
                            @else
                                <div class="w-full h-20 bg-gray-800 rounded flex items-center justify-center" data-type="video" data-src="{{ asset('storage/' . $media->file_path) }}">
                                    <i class="fas fa-play text-white text-2xl"></i>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Room Details Section --}}
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 mb-2">{{ $roomType->name }}</h1>
                <p class="text-2xl font-semibold text-gray-800 mb-4">
                    From ₦{{ number_format($roomType->price, 2) }} / night
                    @php $usd_rate = (float) setting('usd_exchange_rate', 0); @endphp
                    @if($usd_rate > 0)
                        <span class="text-sm text-gray-500 font-normal"> (Approx. ${{ number_format($roomType->price / $usd_rate, 2) }})</span>
                    @endif
                </p>
                
                <p class="text-gray-600 mb-6">{{ $roomType->description }}</p>

                <div class="mb-6 border-t pt-6">
                    <h3 class="text-xl font-semibold mb-4">Room Type Features</h3>
                    <div class="grid grid-cols-2 gap-4 text-gray-700 mb-6">
                        @if(is_array($roomType->features))
                            @foreach($roomType->features as $feature)
                                <div class="flex items-center">
                                    <i class="fas {{ $feature['icon'] }} text-amber-500 mr-3"></i>
                                    <span>{{ $feature['name'] }}</span>
                                </div>
                            @endforeach
                        @endif
                        <div class="flex items-center">
                            <i class="fas fa-users text-amber-500 mr-3"></i>
                            <span>Base: {{ $roomType->base_guests }} Guest(s)</span>
                        </div>
                        @if($roomType->max_guests && $roomType->max_guests > $roomType->base_guests)
                            <div class="flex items-center">
                                <i class="fas fa-user-plus text-amber-500 mr-3"></i>
                                <span>Max: {{ $roomType->max_guests }} Guest(s)</span>
                            </div>
                        @endif
                        @if($roomType->room_size)
                            <div class="flex items-center">
                                <i class="fas fa-ruler-combined text-amber-500 mr-3"></i>
                                <span>{{ $roomType->room_size }} m²</span>
                            </div>
                        @endif
                        @if($roomType->bed_type)
                            <div class="flex items-center">
                                <i class="fas fa-bed text-amber-500 mr-3"></i>
                                <span>{{ ucfirst($roomType->bed_type) }} Bed</span>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Available Units --}}
                    @php
                        $availableUnits = $roomType->activeUnits;
                        $unitCount = $availableUnits->count();
                    @endphp
                    @if($unitCount > 0)
                        <div class="mb-6 border-t pt-6">
                            <h3 class="text-xl font-semibold mb-4">Available Units ({{ $unitCount }})</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($availableUnits as $unit)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium text-gray-900">Unit {{ $unit->unit_number }}</span>
                                            @if($unit->floor)
                                                <span class="text-sm text-gray-500">Floor {{ $unit->floor }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <a href="https://wa.me/{{ setting('whatsapp_number') }}?text=Hi,%20I'm%20interested%20in%20booking%20the%20{{ urlencode($roomType->name) }}." target="_blank" class="whatsapp-link w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded-lg transition duration-300 inline-flex items-center justify-center text-lg">
                    <i class="fab fa-whatsapp mr-3"></i> Reserve This Room Type
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mainMediaDisplay = document.getElementById('main-media-display');
        const thumbnails = document.querySelectorAll('.thumbnail-item');

        thumbnails.forEach(item => {
            item.addEventListener('click', function (e) {
                if (e.target.closest('button')) {
                    return;
                }

                thumbnails.forEach(thumb => thumb.classList.remove('border-amber-500'));
                this.classList.add('border-amber-500');

                const mediaElement = this.querySelector('[data-type]');
                const type = mediaElement.getAttribute('data-type');
                const src = mediaElement.getAttribute('data-src');

                mainMediaDisplay.innerHTML = '';

                if (type === 'image') {
                    const newImage = document.createElement('img');
                    newImage.src = src;
                    newImage.alt = '{{ $roomType->name }}';
                    newImage.className = 'w-full h-full object-cover';
                    mainMediaDisplay.appendChild(newImage);
                } else if (type === 'video') {
                    const newVideo = document.createElement('video');
                    newVideo.src = src;
                    newVideo.className = 'w-full h-full object-cover';
                    newVideo.controls = true;
                    newVideo.autoplay = true;
                    newVideo.muted = true;
                    newVideo.playsinline = true;
                    mainMediaDisplay.appendChild(newVideo);
                }
            });
        });
    });
</script>
@endsection