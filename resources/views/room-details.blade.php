@extends('layouts.app')

@section('title', $roomType->name . ' - Brickspoint Hotel')

@section('content')
    @php $mainMedia = $roomType->media()->where('type', 'image')->first(); @endphp

    @include('partials._page_header', [
        'title' => $roomType->name,
        'eyebrow' => 'Room Details',
        'icon' => 'fa-bed',
        'imageUrl' => $mainMedia ? asset('storage/' . $mainMedia->file_path) : null,
        'subtitle' => $roomType->description,
    ])

    <div class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-16 lg:px-8">
            <nav class="mb-8 text-sm text-gray-500">
                <a href="{{ url('/') }}" class="hover:text-amber-600"><i class="fas fa-home mr-1"></i>Home</a>
                <i class="fas fa-chevron-right mx-2 text-xs text-gray-300"></i>
                <a href="{{ route('rooms') }}" class="hover:text-amber-600">Rooms</a>
                <i class="fas fa-chevron-right mx-2 text-xs text-gray-300"></i>
                <span class="font-semibold text-gray-800">{{ $roomType->name }}</span>
            </nav>

            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:gap-14">
                {{-- Media Gallery Section --}}
                <div>
                    <div id="main-media-display" class="mb-4 aspect-[16/9] overflow-hidden rounded-3xl bg-gray-200 shadow-lift">
                        @if($mainMedia)
                            <img id="main-image" src="{{ asset('storage/' . $mainMedia->file_path) }}" alt="{{ $roomType->name }}" class="h-full w-full object-cover">
                        @else
                            <img id="main-image" src="https://placehold.co/800x533/333333/FFFFFF?text={{ urlencode($roomType->name) }}" alt="{{ $roomType->name }}" class="h-full w-full object-cover">
                        @endif
                    </div>

                    @php $allMedia = $roomType->media; @endphp
                    @if($allMedia->count() > 1)
                        <div class="grid grid-cols-4 gap-2 sm:grid-cols-5 sm:gap-3">
                            @foreach($allMedia as $index => $media)
                                <div class="thumbnail-item group relative cursor-pointer overflow-hidden rounded-xl border-2 border-transparent transition-all hover:border-amber-500 {{ $index === 0 ? 'border-amber-500' : '' }}">
                                    @if ($media->type === 'image')
                                        <img src="{{ thumbnail_url($media->file_path, 'thumb') }}" alt="Room media thumbnail" loading="lazy" class="h-20 w-full object-cover" data-type="image" data-src="{{ asset('storage/' . $media->file_path) }}">
                                    @else
                                        <div class="flex h-20 w-full items-center justify-center bg-gray-800" data-type="video" data-src="{{ asset('storage/' . $media->file_path) }}">
                                            <i class="fas fa-play text-2xl text-white/90 transition group-hover:scale-110"></i>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Room Details Section --}}
                <div class="lg:sticky lg:top-24 lg:self-start">
                    <span class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.25em] text-amber-600"><i class="fas fa-hotel"></i> Brickspoint Hotel</span>
                    <h1 class="mt-2 text-4xl font-extrabold text-gray-900">{{ $roomType->name }}</h1>

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <span class="rounded-full bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2 text-lg font-bold text-white shadow-glow">
                            ₦{{ number_format($roomType->price, 0) }}<span class="text-xs font-medium"> / night</span>
                        </span>
                        @php $usd_rate = (float) setting('usd_exchange_rate', 0); @endphp
                        @if($usd_rate > 0)
                            <span class="text-sm text-gray-500">Approx. ${{ number_format($roomType->price / $usd_rate, 2) }}</span>
                        @endif
                    </div>

                    @if($roomType->weekend_price || $roomType->holiday_price)
                        <p class="mt-3 text-sm text-amber-600">
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
                    @endif

                    <p class="mt-5 leading-relaxed text-gray-600">{{ $roomType->description }}</p>

                    {{-- Room Type Facts --}}
                    <div class="mt-7 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 text-center">
                            <i class="fas fa-users text-xl text-amber-500"></i>
                            <p class="mt-1 text-sm font-bold text-gray-900">{{ $roomType->base_guests }} Guest</p>
                            <p class="text-xs text-gray-500">Base</p>
                        </div>
                        @if($roomType->max_guests && $roomType->max_guests > $roomType->base_guests)
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 text-center">
                                <i class="fas fa-user-plus text-xl text-amber-500"></i>
                                <p class="mt-1 text-sm font-bold text-gray-900">{{ $roomType->max_guests }} Guests</p>
                                <p class="text-xs text-gray-500">Max</p>
                            </div>
                        @endif
                        @if($roomType->room_size)
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 text-center">
                                <i class="fas fa-ruler-combined text-xl text-amber-500"></i>
                                <p class="mt-1 text-sm font-bold text-gray-900">{{ $roomType->room_size }} m²</p>
                                <p class="text-xs text-gray-500">Size</p>
                            </div>
                        @endif
                        @if($roomType->bed_type)
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 text-center">
                                <i class="fas fa-bed text-xl text-amber-500"></i>
                                <p class="mt-1 text-sm font-bold text-gray-900 capitalize">{{ $roomType->bed_type }}</p>
                                <p class="text-xs text-gray-500">Bed</p>
                            </div>
                        @endif
                    </div>

                    {{-- Room Type Features --}}
                    <div class="mt-7">
                        <h3 class="text-xl font-bold text-gray-900">Room Features</h3>
                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @if(is_array($roomType->features))
                                @foreach($roomType->features as $feature)
                                    @if(is_array($feature))
                                        <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white px-4 py-3 shadow-sm">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600"><i class="fas {{ $feature['icon'] ?? 'fa-check' }}"></i></span>
                                            <span class="text-sm font-medium text-gray-700">{{ $feature['name'] ?? '' }}</span>
                                        </div>
                                    @elseif(is_string($feature))
                                        <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white px-4 py-3 shadow-sm">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600"><i class="fas fa-check"></i></span>
                                            <span class="text-sm font-medium text-gray-700">{{ $feature }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- Available Units --}}
                    @php $availableUnits = $roomType->activeUnits; @endphp
                    @if($availableUnits->count() > 0)
                        <div class="mt-7">
                            <h3 class="text-xl font-bold text-gray-900">Available Units ({{ $availableUnits->count() }})</h3>
                            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach($availableUnits as $unit)
                                    <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                        <span class="font-medium text-gray-900"><i class="fas fa-door-open mr-2 text-amber-500"></i>Unit {{ $unit->unit_number }}</span>
                                        @if($unit->floor)
                                            <span class="text-sm text-gray-500">Floor {{ $unit->floor }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Stay Quote Widget --}}
                    <div class="mt-8 rounded-3xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50/40 p-6 sm:p-7" id="stay-quote-widget"
                        data-room-type-id="{{ $roomType->id }}"
                        data-base-guests="{{ $roomType->base_guests }}"
                        data-max-guests="{{ $roomType->getMaxOccupancy() }}"
                        data-room-name="{{ $roomType->name }}">
                        <h3 class="flex items-center gap-2 text-xl font-bold text-gray-900"><i class="fas fa-calculator text-amber-500"></i> Plan Your Stay</h3>
                        <p class="mt-1 text-sm text-gray-600">Select your dates to get an instant quote and live availability.</p>
                        <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div>
                                <label for="quote-check-in" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Check-in</label>
                                <input type="date" id="quote-check-in" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                            </div>
                            <div>
                                <label for="quote-check-out" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Check-out</label>
                                <input type="date" id="quote-check-out" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                            </div>
                            <div>
                                <label for="quote-guests" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Guests</label>
                                <select id="quote-guests" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500"></select>
                            </div>
                        </div>
                        <button type="button" id="quote-btn" class="mt-4 w-full rounded-lg bg-amber-500 px-4 py-3 font-semibold text-white shadow-md shadow-amber-500/30 transition hover:bg-amber-600">
                            <i class="fas fa-calculator mr-2"></i>Check Price &amp; Availability
                        </button>
                        <div id="quote-result" class="mt-4 hidden rounded-xl border border-amber-100 bg-white p-4 text-sm shadow-sm"></div>
                    </div>

                    {{-- Book Now — online booking (create reservation → checkout) --}}
                    <div class="mt-5" id="book-now-widget">
                        <button type="button" id="book-now-btn"
                            class="flex w-full items-center justify-center rounded-xl bg-slate-900 px-6 py-4 text-lg font-bold text-white shadow-lift transition hover:bg-slate-800">
                            <i class="fas fa-calendar-check mr-3 text-amber-400"></i>Book Now
                        </button>

                        <div id="book-now-form-wrap" class="mt-4 hidden rounded-2xl border border-amber-200 bg-white p-5 shadow-lift sm:p-7">
                            <h3 class="text-lg font-bold text-gray-900">Book <span class="text-amber-600">{{ $roomType->name }}</span></h3>
                            <p class="mt-1 text-sm text-gray-600" id="book-now-summary">Select your dates above to see your booking summary.</p>
                            <form id="book-now-form" class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                @include('partials._antibot')
                                <div>
                                    <label for="bn-name" class="mb-1 block text-sm font-semibold text-gray-700">Full name *</label>
                                    <input type="text" id="bn-name" name="guest_name" required maxlength="255"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                </div>
                                <div>
                                    <label for="bn-phone" class="mb-1 block text-sm font-semibold text-gray-700">Phone / WhatsApp *</label>
                                    <input type="tel" id="bn-phone" name="guest_phone" required maxlength="32"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                </div>
                                <div>
                                    <label for="bn-email" class="mb-1 block text-sm font-semibold text-gray-700">Email (optional)</label>
                                    <input type="email" id="bn-email" name="guest_email" maxlength="255"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                </div>
                                <div>
                                    <label for="bn-coupon" class="mb-1 block text-sm font-semibold text-gray-700">Promo code (optional)</label>
                                    <input type="text" id="bn-coupon" name="coupon_code" maxlength="50"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                           placeholder="e.g. WELCOME10">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="bn-requests" class="mb-1 block text-sm font-semibold text-gray-700">Special requests (optional)</label>
                                    <textarea id="bn-requests" name="special_requests" rows="2" maxlength="2000"
                                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
                                </div>
                                <div class="flex flex-col items-start gap-3 sm:col-span-2 sm:flex-row sm:items-center">
                                    <button type="submit" id="bn-submit"
                                            class="inline-flex items-center gap-2 rounded-lg bg-green-500 px-8 py-3 font-bold text-white transition hover:bg-green-600">
                                        <i class="fas fa-paper-plane"></i> Request Booking
                                    </button>
                                    <button type="button" id="bn-cancel" class="text-sm font-semibold text-gray-500 underline hover:text-gray-700">Cancel</button>
                                    <span id="bn-error" class="hidden text-sm font-medium text-red-600"></span>
                                </div>
                            </form>
                            <div id="bn-success" class="mt-4 hidden rounded-lg border border-green-200 bg-green-50 p-4">
                                <p class="font-bold text-green-800"><i class="fas fa-check-circle mr-2"></i>Booking request received!</p>
                                <p id="bn-success-details" class="mt-1 text-sm text-green-700"></p>
                                <div class="mt-3">
                                    <a id="bn-pay-btn" href="#" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-amber-700">
                                        <i class="fas fa-credit-card"></i> Proceed to payment
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="https://wa.me/{{ setting('whatsapp_number') }}?text=Hi,%20I'm%20interested%20in%20booking%20the%20{{ urlencode($roomType->name) }}." target="_blank" id="reserve-whatsapp-link" class="whatsapp-link mt-5 flex w-full items-center justify-center rounded-xl bg-green-500 px-6 py-4 text-lg font-bold text-white transition hover:bg-green-600">
                        <i class="fab fa-whatsapp mr-3"></i> Reserve This Room Type
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Other rooms --}}
    @php
        $otherRooms = \App\Models\RoomType::active()->where('id', '!=', $roomType->id)->orderBy('sort_order')->orderBy('name')->take(3)->get();
    @endphp
    @if($otherRooms->count() > 0)
        <div class="border-t border-gray-100 bg-gray-50">
            <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">Explore Other Rooms</h2>
                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($otherRooms as $other)
                        <a href="{{ route('rooms.show', $other) }}" class="reveal group relative overflow-hidden rounded-2xl bg-white shadow-sm transition-all duration-300 hover:shadow-lift">
                            <div class="aspect-[4/3] overflow-hidden">
                                @if($other->image)
                                    <img src="{{ thumbnail_url($other->image, 'card') }}" alt="{{ $other->name }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                                @else
                                    <img src="https://placehold.co/800x533/333333/FFFFFF?text={{ urlencode($other->name) }}" alt="{{ $other->name }}" loading="lazy" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="p-5">
                                <h3 class="font-bold text-gray-900 group-hover:text-amber-600">{{ $other->name }}</h3>
                                <p class="mt-1 text-sm text-gray-500">From ₦{{ number_format($other->price, 0) }} / night</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

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
                    newImage.className = 'h-full w-full object-cover';
                    mainMediaDisplay.appendChild(newImage);
                } else if (type === 'video') {
                    const newVideo = document.createElement('video');
                    newVideo.src = src;
                    newVideo.className = 'h-full w-full object-cover';
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
                    return '<span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700"><i class="fas fa-check-circle"></i> Available</span>';
                }
                if (status === 'unavailable') {
                    return '<span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700"><i class="fas fa-times-circle"></i> Not available for these dates</span>';
                }
                return '<span class="inline-flex items-center gap-1 rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-600"><i class="fab fa-whatsapp"></i> Confirm via WhatsApp</span>';
            };

            const quoteSkeleton = () => {
                quoteResult.classList.remove('hidden');
                quoteResult.innerHTML =
                    '<div class="animate-pulse space-y-2">' +
                        '<div class="h-4 w-40 rounded bg-gray-200"></div>' +
                        '<div class="h-3 w-full rounded bg-gray-100"></div>' +
                        '<div class="h-3 w-full rounded bg-gray-100"></div>' +
                        '<div class="h-3 w-2/3 rounded bg-gray-100"></div>' +
                        '<div class="mt-3 h-5 w-full rounded bg-gray-200"></div>' +
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
                    rows += '<div class="mt-1 flex justify-between border-t border-gray-200 py-0.5 pt-1"><span class="text-gray-500">Extra guest fee (' + q.guests + ' guests)</span><span>' + naira(q.extra_guest_fee) + '</span></div>';
                }

                let warnings = '';
                q.errors.forEach(msg => {
                    warnings += '<p class="mt-2 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>' + msg + '</p>';
                });
                if (q.availability !== 'available' && q.errors.length === 0 && q.availability !== 'contact_us') {
                    warnings += '<p class="mt-2 text-xs text-red-600"><i class="fas fa-info-circle mr-1"></i>All units are booked or blocked for these dates — try different dates or contact us.</p>';
                }

                quoteResult.classList.remove('hidden');
                quoteResult.innerHTML =
                    '<div class="mb-2 flex items-center justify-between">' +
                        '<span class="font-semibold text-gray-900">' + q.nights + ' night' + (q.nights > 1 ? 's' : '') + ' &middot; ' + q.guests + ' guest' + (q.guests > 1 ? 's' : '') + '</span>' +
                        availabilityBadge(q.availability) +
                    '</div>' +
                    rows +
                    '<div class="mt-2 flex justify-between border-t border-gray-300 pt-2 text-base font-bold text-gray-900"><span>Total</span><span>' + naira(q.total) + '</span></div>' +
                    '<p class="mt-1 text-xs text-gray-400">Estimated total incl. applicable rates. Final confirmation via WhatsApp.</p>' +
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

            [checkInEl, checkOutEl, guestsEl].forEach(el => el.addEventListener('change', function () {
                clearTimeout(quoteDebounce);
                if (checkInEl.value && checkOutEl.value && checkOutEl.value > checkInEl.value) {
                    quoteDebounce = setTimeout(() => runQuote({ silent: false }), 400);
                }
            }));
        }
    });
</script>

<script>
    // ── Book Now — create a reservation, then route to secure checkout ──
    document.addEventListener('DOMContentLoaded', function () {
        const wrap = document.getElementById('book-now-form-wrap');
        const btn = document.getElementById('book-now-btn');
        if (!wrap || !btn) return;

        const quoteWidget = document.getElementById('stay-quote-widget');
        const roomTypeId = quoteWidget ? quoteWidget.dataset.roomTypeId : null;
        const roomName = quoteWidget ? quoteWidget.dataset.roomName : 'this room';

        const checkInEl = document.getElementById('quote-check-in');
        const checkOutEl = document.getElementById('quote-check-out');
        const guestsEl = document.getElementById('quote-guests');

        const form = document.getElementById('book-now-form');
        const submitBtn = document.getElementById('bn-submit');
        const cancelBtn = document.getElementById('bn-cancel');
        const errorEl = document.getElementById('bn-error');
        const successEl = document.getElementById('bn-success');
        const summaryEl = document.getElementById('book-now-summary');

        const naira = (amount) => '₦' + Number(amount).toLocaleString('en-NG', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });

        function fmtDate(v) {
            const d = new Date(v + 'T00:00:00');
            if (isNaN(d)) return v || '';
            return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        function updateSummary() {
            const nights = (checkInEl.value && checkOutEl.value) ?
                Math.round((new Date(checkOutEl.value) - new Date(checkInEl.value)) / 86400000) : null;
            if (checkInEl.value && checkOutEl.value && nights > 0) {
                summaryEl.textContent = fmtDate(checkInEl.value) + ' – ' + fmtDate(checkOutEl.value) +
                    ' (' + nights + ' night' + (nights > 1 ? 's' : '') + ', ' + (guestsEl.value || '1') + ' guest' + (guestsEl.value > 1 ? 's' : '') + ').';
            } else {
                summaryEl.textContent = 'Select your dates above to see your booking summary.';
            }
        }
        [checkInEl, checkOutEl, guestsEl].forEach(el => el && el.addEventListener('change', updateSummary));

        btn.addEventListener('click', function () {
            wrap.classList.toggle('hidden');
            successEl.classList.add('hidden');
            errorEl.classList.add('hidden');
            if (!wrap.classList.contains('hidden')) updateSummary();
        });
        cancelBtn.addEventListener('click', function () {
            wrap.classList.add('hidden');
        });

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!roomTypeId) return;
            errorEl.classList.add('hidden');
            successEl.classList.add('hidden');

            const fd = new FormData(form);
            const payload = {
                room_type_id: roomTypeId,
                check_in: checkInEl.value,
                check_out: checkOutEl.value,
                guests: guestsEl.value,
                guest_name: fd.get('guest_name'),
                guest_phone: fd.get('guest_phone'),
                guest_email: fd.get('guest_email') || null,
                special_requests: fd.get('special_requests') || null,
                coupon_code: fd.get('coupon_code') || null,
                ...window.collectAntiBot(form),
            };

            submitBtn.disabled = true;
            const originalHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending…';

            try {
                const res = await fetch('/api/reservations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload),
                });
                const body = await res.json().catch(() => null);

                if (res.status === 201 && body && body.success) {
                    form.classList.add('hidden');
                    document.getElementById('bn-success-details').textContent =
                        'Reference ' + body.reference + ' — quoted total ' + naira(body.total) +
                        '. We will contact you at ' + (payload.guest_phone || payload.guest_email) + ' shortly.';
                    const payBtn = document.getElementById('bn-pay-btn');
                    if (payBtn && body.payment_url) {
                        payBtn.href = body.payment_url;
                        payBtn.classList.remove('hidden');
                    }
                    successEl.classList.remove('hidden');
                } else {
                    throw new Error(body && body.message ? body.message : 'Could not submit your request. Please try again or contact us.');
                }
            } catch (err) {
                errorEl.textContent = err.message || 'Something went wrong. Please try again.';
                errorEl.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        });
    });
</script>
@endsection