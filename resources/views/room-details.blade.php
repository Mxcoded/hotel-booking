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
                                <img src="{{ thumbnail_url($media->file_path, 'thumb') }}" alt="Room media thumbnail" class="w-full h-20 object-cover rounded" data-type="image" data-src="{{ asset('storage/' . $media->file_path) }}">
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
                <p class="text-2xl font-semibold text-gray-800 mb-1">
                    From ₦{{ number_format($roomType->price, 2) }} / night
                    @php $usd_rate = (float) setting('usd_exchange_rate', 0); @endphp
                    @if($usd_rate > 0)
                        <span class="text-sm text-gray-500 font-normal"> (Approx. ${{ number_format($roomType->price / $usd_rate, 2) }})</span>
                    @endif
                </p>
                @if($roomType->weekend_price || $roomType->holiday_price)
                    <p class="text-sm text-amber-600 mb-4">
                        <i class="fas fa-calendar-day mr-1"></i>
                        @if($roomType->weekend_price)
                            ₦{{ number_format($roomType->weekend_price, 0) }} on Fri &amp; Sat
                        @endif
                        @if($roomType->weekend_price && $roomType->holiday_price)
                            &middot;
                        @endif
                        @if($roomType->holiday_price)
                            ₦{{ number_format($roomType->holiday_price, 0) }} on public holidays
                        @endif
                    </p>
                @else
                    <div class="mb-4"></div>
                @endif
                
                <p class="text-gray-600 mb-6">{{ $roomType->description }}</p>

                <div class="mb-6 border-t pt-6">
                    <h3 class="text-xl font-semibold mb-4">Room Type Features</h3>
                    <div class="grid grid-cols-2 gap-4 text-gray-700 mb-6">
                        @if(is_array($roomType->features))
                            @foreach($roomType->features as $feature)
                                @if(is_array($feature))
                                    <div class="flex items-center">
                                        <i class="fas {{ $feature['icon'] ?? '' }} text-amber-500 mr-3"></i>
                                        <span>{{ $feature['name'] ?? '' }}</span>
                                    </div>
                                @elseif(is_string($feature))
                                    <div class="flex items-center">
                                        <i class="fas fa-check text-amber-500 mr-3"></i>
                                        <span>{{ $feature }}</span>
                                    </div>
                                @endif
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

                {{-- Stay Quote Widget --}}
                <div class="mb-6 border-t pt-6" id="stay-quote-widget"
                    data-room-type-id="{{ $roomType->id }}"
                    data-base-guests="{{ $roomType->base_guests }}"
                    data-max-guests="{{ $roomType->getMaxOccupancy() }}"
                    data-room-name="{{ $roomType->name }}">
                    <h3 class="text-xl font-semibold mb-4">Plan Your Stay</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                        <div>
                            <label for="quote-check-in" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Check-in</label>
                            <input type="date" id="quote-check-in" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        </div>
                        <div>
                            <label for="quote-check-out" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Check-out</label>
                            <input type="date" id="quote-check-out" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        </div>
                        <div>
                            <label for="quote-guests" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Guests</label>
                            <select id="quote-guests" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm"></select>
                        </div>
                    </div>
                    <button type="button" id="quote-btn" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-300 text-sm">
                        <i class="fas fa-calculator mr-2"></i>Check Price &amp; Availability
                    </button>
                    <div id="quote-result" class="hidden mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm"></div>
                </div>

                <a href="https://wa.me/{{ setting('whatsapp_number') }}?text=Hi,%20I'm%20interested%20in%20booking%20the%20{{ urlencode($roomType->name) }}." target="_blank" id="reserve-whatsapp-link" class="whatsapp-link w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded-lg transition duration-300 inline-flex items-center justify-center text-lg">
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

        // ── Stay Quote Widget ──────────────────────────────
        const quoteWidget = document.getElementById('stay-quote-widget');
        if (quoteWidget) {
            const roomTypeId = quoteWidget.dataset.roomTypeId;
            const baseGuests = parseInt(quoteWidget.dataset.baseGuests, 10) || 1;
            const maxGuests = parseInt(quoteWidget.dataset.maxGuests, 10) || baseGuests;
            const roomName = quoteWidget.dataset.roomName;

            const checkInEl = document.getElementById('quote-check-in');
            const checkOutEl = document.getElementById('quote-check-out');
            const guestsEl = document.getElementById('quote-guests');
            const quoteBtn = document.getElementById('quote-btn');
            const quoteResult = document.getElementById('quote-result');
            const reserveLink = document.getElementById('reserve-whatsapp-link');

            const todayStr = new Date().toISOString().slice(0, 10);
            checkInEl.min = todayStr;

            for (let g = 1; g <= maxGuests; g++) {
                const opt = document.createElement('option');
                opt.value = g;
                opt.textContent = g + (g === 1 ? ' Guest' : ' Guests');
                if (g === baseGuests) opt.selected = true;
                guestsEl.appendChild(opt);
            }

            let activeQuote = null;
            let quoteDebounce = null;
            const quoteCache = new Map();

            checkInEl.addEventListener('change', function () {
                if (!this.value) return;
                const next = new Date(this.value);
                next.setDate(next.getDate() + 1);
                checkOutEl.min = next.toISOString().slice(0, 10);
                if (checkOutEl.value && checkOutEl.value <= this.value) {
                    checkOutEl.value = checkOutEl.min;
                }
            });

            const naira = (amount) => '₦' + Number(amount).toLocaleString('en-NG', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });

            const availabilityBadge = (status) => {
                if (status === 'available') {
                    return '<span class="inline-flex items-center gap-1 text-green-700 bg-green-100 rounded-full px-2.5 py-0.5 text-xs font-semibold"><i class="fas fa-check-circle"></i> Available</span>';
                }
                if (status === 'unavailable') {
                    return '<span class="inline-flex items-center gap-1 text-red-700 bg-red-100 rounded-full px-2.5 py-0.5 text-xs font-semibold"><i class="fas fa-times-circle"></i> Not available for these dates</span>';
                }
                return '<span class="inline-flex items-center gap-1 text-gray-600 bg-gray-200 rounded-full px-2.5 py-0.5 text-xs font-semibold"><i class="fab fa-whatsapp"></i> Confirm via WhatsApp</span>';
            };

            const quoteSkeleton = () => {
                quoteResult.classList.remove('hidden');
                quoteResult.innerHTML =
                    '<div class="animate-pulse space-y-2">' +
                        '<div class="h-4 w-40 rounded bg-gray-200"></div>' +
                        '<div class="h-3 w-full rounded bg-gray-100"></div>' +
                        '<div class="h-3 w-full rounded bg-gray-100"></div>' +
                        '<div class="h-3 w-2/3 rounded bg-gray-100"></div>' +
                        '<div class="h-5 w-full rounded bg-gray-200 mt-3"></div>' +
                    '</div>';
            };

            const updateReserveLink = (q) => {
                const lines = [
                    "Hi, I'd like to book the " + roomName + ".",
                    'Check-in: ' + q.nightly[0].date,
                    'Check-out: ' + checkOutEl.value,
                    'Nights: ' + q.nights,
                    'Guests: ' + q.guests,
                    'Estimated total: ' + naira(q.total)
                ];
                reserveLink.href = 'https://wa.me/{{ setting("whatsapp_number") }}?text=' + encodeURIComponent(lines.join('\n'));
            };

            async function fetchQuote(params) {
                const response = await fetch('{{ route('api.stay-quote') }}?' + params.toString(), {
                    signal: activeQuote.signal
                });
                if (!response.ok) throw new Error('Quote failed');
                return response.json();
            }

            function renderQuote(q) {
                let rows = q.nightly.map(n =>
                    '<div class="flex justify-between py-0.5"><span class="text-gray-500">' + n.date + '</span><span>' + naira(n.price) + '</span></div>'
                ).join('');

                if (q.extra_guest_fee > 0) {
                    rows += '<div class="flex justify-between py-0.5 border-t border-gray-200 mt-1 pt-1"><span class="text-gray-500">Extra guest fee (' + q.guests + ' guests)</span><span>' + naira(q.extra_guest_fee) + '</span></div>';
                }

                let warnings = '';
                q.errors.forEach(msg => {
                    warnings += '<p class="text-xs text-red-600 mt-2"><i class="fas fa-exclamation-circle mr-1"></i>' + msg + '</p>';
                });
                if (q.availability !== 'available' && q.errors.length === 0 && q.availability !== 'contact_us') {
                    warnings += '<p class="text-xs text-red-600 mt-2"><i class="fas fa-info-circle mr-1"></i>All units are booked or blocked for these dates — try different dates or contact us.</p>';
                }

                quoteResult.classList.remove('hidden');
                quoteResult.innerHTML =
                    '<div class="flex items-center justify-between mb-2">' +
                        '<span class="font-semibold text-gray-900">' + q.nights + ' night' + (q.nights > 1 ? 's' : '') + ' &middot; ' + q.guests + ' guest' + (q.guests > 1 ? 's' : '') + '</span>' +
                        availabilityBadge(q.availability) +
                    '</div>' +
                    rows +
                    '<div class="flex justify-between border-t border-gray-300 mt-2 pt-2 font-bold text-base text-gray-900"><span>Total</span><span>' + naira(q.total) + '</span></div>' +
                    '<p class="text-xs text-gray-400 mt-1">Estimated total incl. applicable rates. Final confirmation via WhatsApp.</p>' +
                    warnings;

                updateReserveLink(q);
            }

            async function runQuote({ silent }) {
                if (!checkInEl.value || !checkOutEl.value) {
                    if (!silent) {
                        quoteResult.classList.remove('hidden');
                        quoteResult.innerHTML = '<p class="text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>Please select your check-in and check-out dates.</p>';
                    }
                    return;
                }

                const params = new URLSearchParams({
                    room_type_id: roomTypeId,
                    check_in: checkInEl.value,
                    check_out: checkOutEl.value,
                    guests: guestsEl.value
                });
                const cacheKey = params.toString();

                if (quoteCache.has(cacheKey)) {
                    renderQuote(quoteCache.get(cacheKey));
                    // Keep the cached view honest with a background refresh
                    fetchQuote(params).then(q => {
                        quoteCache.set(cacheKey, q);
                        renderQuote(q);
                    }).catch(() => {});
                    return;
                }

                if (activeQuote) activeQuote.abort();
                activeQuote = new AbortController();

                if (!silent) {
                    quoteBtn.disabled = true;
                    quoteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Checking...';
                }
                quoteSkeleton();

                try {
                    const q = await fetchQuote(params);
                    quoteCache.set(cacheKey, q);
                    renderQuote(q);
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        quoteResult.classList.remove('hidden');
                        quoteResult.innerHTML = '<p class="text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>Could not calculate your stay right now. Please try again.</p>';
                    }
                } finally {
                    if (!silent) {
                        quoteBtn.disabled = false;
                        quoteBtn.innerHTML = '<i class="fas fa-calculator mr-2"></i>Check Price &amp; Availability';
                    }
                }
            }

            quoteBtn.addEventListener('click', function () {
                clearTimeout(quoteDebounce);
                runQuote({ silent: false });
            });

            // ── Live quotes: debounced auto-run as the criteria change ──
            [checkInEl, checkOutEl, guestsEl].forEach(el => el.addEventListener('change', function () {
                clearTimeout(quoteDebounce);
                if (checkInEl.value && checkOutEl.value && checkOutEl.value > checkInEl.value) {
                    quoteDebounce = setTimeout(() => runQuote({ silent: false }), 400);
                }
            }));
        }
    });
</script>
@endsection