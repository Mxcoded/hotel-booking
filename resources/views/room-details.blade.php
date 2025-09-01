@extends('layouts.app')

@section('title', $room->name . ' - Brickspoint Boutique Aparthotel')

@section('content')
<div class="bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- Media Gallery Section -->
            <div>
                <!-- Main Media Display -->
                <div id="main-media-display" class="mb-4 rounded-lg overflow-hidden shadow-lg aspect-w-16 aspect-h-9 bg-gray-200">
                    @if($room->media->where('type', 'image')->first())
                        <img id="main-image" src="{{ asset('storage/' . $room->media->where('type', 'image')->first()->file_path) }}" alt="{{ $room->name }}" class="w-full h-full object-cover">
                    @else
                         <img id="main-image" src="{{ asset('storage/' . $room->image) }}" alt="{{ $room->name }}" class="w-full h-full object-cover">
                    @endif
                </div>

                <!-- Thumbnails -->
                <div class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                    <!-- Main Room Image Thumbnail -->
                    <div class="cursor-pointer border-2 border-green-500 rounded p-1 thumbnail-item">
                        <img src="{{ asset('storage/' . $room->image) }}" alt="Main image thumbnail" class="w-full h-20 object-cover rounded" data-type="image" data-src="{{ asset('storage/' . $room->image) }}">
                    </div>

                    @foreach($room->media as $media)
                        <div class="relative group cursor-pointer border-2 border-transparent hover:border-green-500 rounded p-1 thumbnail-item">
                             @if ($media->type === 'image')
                                <img src="{{ asset('storage/' . $media->file_path) }}" alt="Room media thumbnail" class="w-full h-20 object-cover rounded" data-type="image" data-src="{{ asset('storage/' . $media->file_path) }}">
                            @else
                                <div class="w-full h-20 bg-gray-800 rounded flex items-center justify-center" data-type="video" data-src="{{ asset('storage/' . $media->file_path) }}">
                                    <i class="fas fa-play text-white text-2xl"></i>
                                </div>
                            @endif
                            
                            {{-- Delete Button for Admins --}}
                            @auth
                                <form action="{{ route('admin.rooms.media.destroy', $media) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="absolute top-0 right-0 m-1 bg-red-600 text-white rounded-full h-6 w-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </form>
                            @endauth
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Room Details Section -->
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 mb-2">{{ $room->name }}</h1>
                <p class="text-2xl font-semibold text-gray-800 mb-4">
                    From ₦{{ number_format($room->price, 2) }} / night
                    @php $usd_rate = (float) setting('usd_exchange_rate', 0); @endphp
                    @if($usd_rate > 0)
                        <span class="text-sm text-gray-500 font-normal"> (Approx. ${{ number_format($room->price / $usd_rate, 2) }})</span>
                    @endif
                </p>
                
                <p class="text-gray-600 mb-6">{{ $room->description }}</p>

                <div class="mb-6 border-t pt-6">
                    <h3 class="text-xl font-semibold mb-4">Room Features</h3>
                    <div class="grid grid-cols-2 gap-4 text-gray-700">
                        @if(is_array($room->features))
                            @foreach($room->features as $feature)
                                <div class="flex items-center">
                                    <i class="fas {{ $feature['icon'] }} text-green-500 mr-3"></i>
                                    <span>{{ $feature['name'] }}</span>
                                </div>
                            @endforeach
                        @endif
                        <div class="flex items-center">
                            <i class="fas fa-users text-green-500 mr-3"></i>
                            <span>Up to {{ $room->guests }} Guest(s)</span>
                        </div>
                    </div>
                </div>

                <a href="https://wa.me/{{ setting('whatsapp_number') }}?text=Hi,%20I'm%20interested%20in%20booking%20the%20{{ urlencode($room->name) }}." target="_blank" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded-lg transition duration-300 inline-flex items-center justify-center text-lg">
                    <i class="fab fa-whatsapp mr-3"></i> Reserve This Room
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
                // Prevent the click from triggering if the delete button was clicked
                if (e.target.closest('button')) {
                    return;
                }

                // Remove active border from all thumbnails
                thumbnails.forEach(thumb => thumb.classList.remove('border-green-500'));
                // Add active border to clicked thumbnail
                this.classList.add('border-green-500');

                const mediaElement = this.querySelector('[data-type]');
                const type = mediaElement.getAttribute('data-type');
                const src = mediaElement.getAttribute('data-src');

                // Clear the main display
                mainMediaDisplay.innerHTML = '';

                if (type === 'image') {
                    const newImage = document.createElement('img');
                    newImage.src = src;
                    newImage.alt = '{{ $room->name }}';
                    newImage.className = 'w-full h-full object-cover';
                    mainMediaDisplay.appendChild(newImage);
                } else if (type === 'video') {
                    const newVideo = document.createElement('video');
                    newVideo.src = src;
                    newVideo.className = 'w-full h-full object-cover';
                    newVideo.controls = true;
                    newVideo.autoplay = true;
                    newVideo.muted = true; // Autoplay often requires video to be muted
                    newVideo.playsinline = true;
                    mainMediaDisplay.appendChild(newVideo);
                }
            });
        });
    });
</script>
@endsection

