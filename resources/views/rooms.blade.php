@extends('layouts.app')

@php
    $headerRoom = $roomTypes->first(fn ($r) => $r->image);
    $headerImg = $headerRoom ? thumbnail_url($headerRoom->image, 'card') : null;
@endphp

@section('content')

    @include('partials._page_header', [
        'title' => 'Our Rooms & Suites',
        'eyebrow' => 'Stay With Us',
        'icon' => 'fa-hotel',
        'imageUrl' => $headerImg,
        'subtitle' => 'Find the perfect space for your stay. Each room type offers multiple units designed with your comfort in mind.',
    ])

    {{-- Availability Search --}}
    <div class="border-b border-amber-100 bg-gradient-to-r from-amber-50 to-orange-50/50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <h2 class="mb-5 flex items-center gap-2 text-lg font-bold text-gray-800">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-500 text-sm text-white"><i class="fas fa-calendar-check"></i></span>
                Check Availability &amp; Book Online
            </h2>
            <form id="availability-form" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
                <div>
                    <label for="avail-check-in" class="mb-1.5 block text-sm font-semibold text-gray-700">Check-in</label>
                    <input type="date" id="avail-check-in" name="check_in" required
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 shadow-sm transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
                <div>
                    <label for="avail-check-out" class="mb-1.5 block text-sm font-semibold text-gray-700">Check-out</label>
                    <input type="date" id="avail-check-out" name="check_out" required
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 shadow-sm transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
                <div>
                    <label for="avail-guests" class="mb-1.5 block text-sm font-semibold text-gray-700">Guests</label>
                    <select id="avail-guests" name="guests"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 shadow-sm transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'Guest' : 'Guests' }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" id="avail-submit"
                        class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-2.5 font-bold text-white shadow-glow transition hover:from-amber-600 hover:to-amber-700">
                    <i class="fas fa-magnifying-glass mr-2"></i> Check Availability
                </button>
            </form>

            <p id="availability-error" class="mt-3 hidden text-sm font-medium text-red-600"></p>

            <div id="availability-results" class="mt-6 hidden space-y-4" aria-live="polite"></div>

            {{-- Inline booking request form --}}
            <div id="booking-section" class="mt-6 hidden">
                <div class="rounded-2xl border border-green-200 bg-white p-5 shadow-lift">
                    <h3 class="flex items-center gap-2 font-bold text-gray-900">
                        <i class="fas fa-check-circle text-green-500"></i>
                        Request Booking — <span id="booking-room-name" class="text-amber-600"></span>
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">No payment needed now. Our team will confirm availability and get back to you.</p>
                    <form id="booking-form" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @include('partials._antibot')
                        <div>
                            <label for="bk-name" class="mb-1.5 block text-sm font-semibold text-gray-700">Full name *</label>
                            <input type="text" id="bk-name" name="guest_name" required maxlength="255"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label for="bk-phone" class="mb-1.5 block text-sm font-semibold text-gray-700">Phone / WhatsApp *</label>
                            <input type="tel" id="bk-phone" name="guest_phone" required maxlength="32"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label for="bk-email" class="mb-1.5 block text-sm font-semibold text-gray-700">Email (optional)</label>
                            <input type="email" id="bk-email" name="guest_email" maxlength="255"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label for="bk-guests-display" class="mb-1.5 block text-sm font-semibold text-gray-700">Guests</label>
                            <input type="text" id="bk-guests-display" disabled
                                   class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-600">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="bk-requests" class="mb-1.5 block text-sm font-semibold text-gray-700">Special requests (optional)</label>
                            <textarea id="bk-requests" name="special_requests" rows="2" maxlength="2000"
                                      class="w-full rounded-lg border border-gray-300 px-4 py-2.5 transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                        </div>
                        <div class="sm:col-span-1">
                            <label for="bk-coupon" class="mb-1.5 block text-sm font-semibold text-gray-700">Promo code (optional)</label>
                            <input type="text" id="bk-coupon" name="coupon_code" maxlength="50"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:outline-none"
                                   placeholder="e.g. WELCOME10">
                        </div>
                        <div class="flex flex-col items-start gap-3 sm:col-span-2 sm:flex-row sm:items-center">
                            <button type="submit" id="booking-submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-green-500 px-8 py-3 font-bold text-white transition hover:bg-green-600">
                                <i class="fas fa-paper-plane"></i>Send Request
                            </button>
                            <button type="button" id="booking-cancel-btn" class="text-sm font-semibold text-gray-500 underline hover:text-gray-700">
                                Cancel
                            </button>
                            <span id="booking-error" class="hidden text-sm font-medium text-red-600"></span>
                        </div>
                    </form>
                    <div id="booking-success" class="mt-2 hidden rounded-lg border border-green-200 bg-green-50 p-4">
                        <p class="font-bold text-green-800"><i class="fas fa-check-circle mr-2"></i>Booking request received!</p>
                        <p id="booking-success-details" class="mt-1 text-sm text-green-700"></p>
                        <div class="mt-3">
                            <a id="booking-pay-btn" href="#" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2 text-sm font-bold text-white shadow-glow transition hover:from-amber-600 hover:to-amber-700">
                                <i class="fas fa-credit-card"></i> Proceed to payment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rooms Listing Section --}}
    <div class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
            <div class="mb-12 text-center">
                <span class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.25em] text-amber-600"><i class="fas fa-bed"></i> Accommodations</span>
                <h2 class="mt-3 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">Choose Your Room</h2>
                <p class="mx-auto mt-3 max-w-2xl text-lg text-gray-600">Every room is thoughtfully designed with premium bedding, en-suite facilities, and modern comforts to help you feel right at home in Abuja.</p>
            </div>

            @if($roomTypes->count() > 0)
                <div class="space-y-8">
                    @php
                        $usd_rate = (float) setting('usd_exchange_rate', 0);
                    @endphp

                    @foreach($roomTypes as $roomType)
                        @php
                            $availableUnits = $roomType->activeUnits()->count();
                            $basePrice = $roomType->price;
                        @endphp
                        <div class="reveal group relative grid grid-cols-1 gap-0 overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:shadow-lift lg:grid-cols-[2fr_3fr]">
                            <div class="relative overflow-hidden">
                                <button onclick="toggleFavorite('roomtype-{{ $roomType->id }}')" class="favorite-btn absolute right-4 top-4 z-10 rounded-full bg-white/90 p-2.5 shadow transition-transform duration-200 hover:scale-110" data-room-id="roomtype-{{ $roomType->id }}">
                                    <i class="far fa-heart text-xl text-gray-700"></i>
                                </button>
                                <a href="{{ route('rooms.show', $roomType) }}" class="block h-full overflow-hidden">
                                    @if($roomType->image)
                                        <img src="{{ thumbnail_url($roomType->image, 'card') }}" alt="{{ $roomType->name }}"
                                            class="h-64 w-full object-cover transition-transform duration-700 group-hover:scale-110 lg:h-full">
                                    @else
                                        <img src="https://placehold.co/800x533/333333/FFFFFF?text={{ urlencode($roomType->name) }}" alt="{{ $roomType->name }}"
                                            class="h-64 w-full object-cover transition-transform duration-700 group-hover:scale-110 lg:h-full">
                                    @endif
                                </a>
                                @if($availableUnits > 1)
                                    <span class="absolute bottom-3 left-3 rounded-full bg-green-500/90 px-3 py-1 text-xs font-bold text-white backdrop-blur">
                                        <i class="fas fa-door-open mr-1"></i> {{ $availableUnits }} Units
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-col justify-between gap-6 p-6 sm:p-8">
                                <div>
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <h3 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">
                                            <a href="{{ route('rooms.show', $roomType) }}" class="hover:text-amber-600 transition-colors">{{ $roomType->name }}</a>
                                        </h3>
                                        <div class="text-right">
                                            <p class="text-2xl font-bold text-amber-600">₦{{ number_format($basePrice, 0) }}<span class="text-sm font-medium text-gray-500"> / night</span></p>
                                            @if($usd_rate > 0)
                                                <p class="text-xs text-gray-500">Approx. ${{ number_format($basePrice / $usd_rate, 2) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="mt-3 text-gray-600">{{ $roomType->description }}</p>

                                    <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
                                        @if(isset($roomType->features) && is_array($roomType->features))
                                            @foreach($roomType->features as $feature)
                                                @if(is_array($feature) && !empty($feature['icon']))
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-50 border border-gray-200 px-3 py-1.5 text-gray-600">
                                                        <i class="fas {{ $feature['icon'] }} text-amber-500"></i> {{ $feature['name'] ?? '' }}
                                                    </span>
                                                @elseif(is_string($feature))
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-50 border border-gray-200 px-3 py-1.5 text-gray-600">
                                                        <i class="fas fa-check text-amber-500"></i> {{ $feature }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @endif
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-50 border border-gray-200 px-3 py-1.5 text-gray-600"><i class="fas fa-users text-amber-500"></i> {{ $roomType->base_guests }} Guest(s)</span>
                                        @if($roomType->max_guests && $roomType->max_guests > $roomType->base_guests)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200 px-3 py-1.5 text-amber-700"><i class="fas fa-user-plus"></i> Up to {{ $roomType->max_guests }}</span>
                                        @endif
                                    </div>

                                    @if($roomType->weekend_price)
                                        <p class="mt-4 text-xs text-gray-500"><i class="fas fa-calendar-day mr-1 text-amber-500"></i>₦{{ number_format($roomType->weekend_price, 0) }} on Fri &amp; Sat</p>
                                    @endif
                                </div>

                                <div class="flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row">
                                    <a href="{{ route('rooms.show', $roomType) }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-amber-500 px-6 py-3 font-bold text-white shadow-md shadow-amber-500/30 transition hover:bg-amber-600">
                                        View Details
                                    </a>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('whatsapp_number', '+2348099999620')) }}?text=Hi,%20I'm%20interested%20in%20booking%20the%20{{ urlencode($roomType->name) }}." target="_blank"
                                        class="whatsapp-link flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-green-200 bg-green-50 px-6 py-3 font-semibold text-green-700 transition hover:bg-green-600 hover:text-white">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mx-auto max-w-xl text-center">
                    <i class="fas fa-bed text-5xl text-amber-300"></i>
                    <p class="mt-4 text-xl text-gray-600">We are currently updating our room listings. Please check back soon for our beautiful accommodations!</p>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('whatsapp_number', '+2348099999620')) }}" target="_blank" class="whatsapp-link mt-6 inline-flex items-center gap-2 rounded-full bg-green-500 px-6 py-3 font-bold text-white transition hover:bg-green-600">
                        <i class="fab fa-whatsapp"></i> Enquire on WhatsApp
                    </a>
                </div>
            @endif
        </div>
    </div>

    @php $wa_number = preg_replace('/[^0-9]/', '', setting('whatsapp_number', '+2348099999620')); @endphp

    <script>
    (function () {
        const form = document.getElementById('availability-form');
        const resultsEl = document.getElementById('availability-results');
        const errorEl = document.getElementById('availability-error');
        const submitBtn = document.getElementById('avail-submit');
        const checkInEl = document.getElementById('avail-check-in');
        const checkOutEl = document.getElementById('avail-check-out');
        const guestsEl = document.getElementById('avail-guests');
        const waNumber = '{{ $wa_number }}';
        let lastResults = [];
        let activeSearch = null;
        let debounceTimer = null;
        let silentRefresh = null;
        const resultCache = new Map();

        const todayStr = new Date().toISOString().split('T')[0];
        checkInEl.min = todayStr;
        checkOutEl.min = todayStr;

        function toISO(d) {
            return d.toISOString().split('T')[0];
        }

        function syncCheckoutMin() {
            if (!checkInEl.value) return;
            const next = new Date(checkInEl.value + 'T00:00:00');
            next.setDate(next.getDate() + 1);
            checkOutEl.min = toISO(next);
            if (!checkOutEl.value || checkOutEl.value <= checkInEl.value) {
                checkOutEl.value = toISO(next);
            }
        }

        function esc(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        function badge(status, unitsFree) {
            if (status === 'available') {
                return '<span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800"><i class="fas fa-check-circle mr-1"></i>Available' + (unitsFree > 1 ? ' — ' + unitsFree + ' rooms left' : '') + '</span>';
            }
            if (status === 'contact_us') {
                return '<span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800"><i class="fas fa-phone mr-1"></i>Contact us to book</span>';
            }
            return '<span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700"><i class="fas fa-times-circle mr-1"></i>Not available</span>';
        }

        function waLink(r) {
            const msg = "Hi, I'd like to book the " + r.name + ' from ' + r.check_in + ' to ' + r.check_out +
                ' (' + r.nights + ' night(s), ' + r.guests + ' guest(s))';
            if (r.status === 'available') {
                return msg + '. Quoted total: NGN ' + Number(r.total).toLocaleString() + '.';
            }
            return msg + '.';
        }

        function skeleton() {
            const card = () => '<div class="flex flex-col sm:flex-row sm:items-center gap-4 rounded-2xl border border-amber-100 bg-white p-4 shadow-sm">' +
                '<div class="h-24 w-full sm:w-36 animate-pulse rounded-md bg-gray-200"></div>' +
                '<div class="flex-grow space-y-2">' +
                    '<div class="h-4 w-28 animate-pulse rounded bg-gray-200"></div>' +
                    '<div class="h-5 w-48 animate-pulse rounded bg-gray-200"></div>' +
                    '<div class="h-4 w-64 max-w-full animate-pulse rounded bg-gray-100"></div>' +
                '</div>' +
                '<div class="flex flex-row sm:flex-col gap-2 shrink-0">' +
                    '<div class="h-9 w-20 animate-pulse rounded-lg bg-gray-200"></div>' +
                    '<div class="h-9 w-20 animate-pulse rounded-lg bg-gray-200"></div>' +
                '</div></div>';
            resultsEl.innerHTML =
                '<p class="text-sm font-semibold text-gray-700">Checking live availability…</p>' +
                card() +
                '<div class="hidden sm:block">' + card() + '</div>';
            resultsEl.classList.remove('hidden');
        }

        function render(data) {
            resultsEl.innerHTML = '';
            lastResults = data.results;
            const header = document.createElement('p');
            header.className = 'text-sm font-semibold text-gray-700';
            header.textContent = data.results.length + ' room type(s) for ' + data.nights +
                ' night(s), ' + data.guests + ' guest(s):';
            resultsEl.appendChild(header);

            data.results.forEach(r => {
                const card = document.createElement('div');
                card.className = 'flex flex-col sm:flex-row sm:items-center gap-4 rounded-2xl border border-amber-100 bg-white p-4 shadow-sm transition-shadow hover:shadow-md';
                const img = r.image_url
                    ? '<img src="' + esc(r.image_url) + '" alt="' + esc(r.name) + '" loading="lazy" class="h-24 w-full sm:w-36 object-cover rounded-md">'
                    : '<div class="h-24 w-full sm:w-36 rounded-md bg-gray-200 flex items-center justify-center"><i class="fas fa-bed text-gray-400 text-2xl"></i></div>';

                let errHtml = '';
                if (r.errors.length) {
                    errHtml = '<ul class="mt-1 text-xs text-red-600 list-disc list-inside">' +
                        r.errors.map(e => '<li>' + esc(e) + '</li>').join('') + '</ul>';
                }

                card.innerHTML =
                    img +
                    '<div class="flex-grow">' +
                        badge(r.status, r.units_free) +
                        '<h3 class="mt-2 font-bold text-gray-900">' + esc(r.name) + '</h3>' +
                        (r.errors.length || r.status !== 'unavailable'
                            ? '<p class="text-sm text-gray-600">Total for ' + r.nights + ' night(s): <strong>₦' + Number(r.total).toLocaleString() + '</strong></p>'
                            : '') +
                        errHtml +
                    '</div>' +
                    '<div class="flex flex-row sm:flex-col gap-2 shrink-0">' +
                        '<a href="' + esc(r.url) + '" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg text-sm">View</a>' +
                        (r.status === 'available'
                            ? '<button type="button" data-request-id="' + r.id + '" class="request-btn flex-1 text-center bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2 px-4 rounded-lg text-sm"><i class="fas fa-calendar-check mr-1"></i>Request</button>'
                            : '') +
                        '<a href="https://wa.me/' + waNumber + '?text=' + encodeURIComponent(waLink(r)) + '" target="_blank" class="whatsapp-link flex-1 text-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg text-sm"><i class="fab fa-whatsapp mr-1"></i>Book</a>' +
                    '</div>';
                resultsEl.appendChild(card);
            });

            resultsEl.classList.remove('hidden');
        }

        function currentParams() {
            return new URLSearchParams({
                check_in: checkInEl.value,
                check_out: checkOutEl.value,
                guests: guestsEl.value,
            });
        }

        async function runSearch() {
            errorEl.classList.add('hidden');

            const params = currentParams();
            const cacheKey = params.toString();

            if (resultCache.has(cacheKey)) {
                render(resultCache.get(cacheKey));
                refreshSilently(cacheKey);
                return;
            }

            if (activeSearch) activeSearch.abort();
            activeSearch = new AbortController();
            skeleton();

            submitBtn.disabled = true;
            const originalHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Searching…';

            try {
                const res = await fetch('/api/availability?' + cacheKey, { signal: activeSearch.signal });
                if (!res.ok) {
                    const body = await res.json().catch(() => null);
                    throw new Error(body && body.message ? body.message : 'Please check your dates and try again.');
                }
                const data = await res.json();
                resultCache.set(cacheKey, data);
                render(data);
            } catch (err) {
                if (err.name !== 'AbortError') {
                    resultsEl.classList.add('hidden');
                    errorEl.textContent = err.message || 'Something went wrong. Please try again.';
                    errorEl.classList.remove('hidden');
                }
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }

        async function refreshSilently(cacheKey) {
            try {
                const res = await fetch('/api/availability?' + cacheKey);
                if (!res.ok) return;
                const data = await res.json();
                resultCache.set(cacheKey, data);
                if (currentParams().toString() === cacheKey) {
                    render(data);
                }
            } catch (err) { /* background refresh is best-effort */ }
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearTimeout(debounceTimer);
            runSearch();
        });

        // ── Live search: debounced auto-run as the criteria change ──
        [checkInEl, checkOutEl].forEach(el => el.addEventListener('change', function () {
            syncCheckoutMin();
            clearTimeout(debounceTimer);
            if (checkInEl.value && checkOutEl.value && checkOutEl.value > checkInEl.value) {
                debounceTimer = setTimeout(runSearch, 400);
            }
        }));
        guestsEl.addEventListener('change', function () {
            clearTimeout(debounceTimer);
            if (checkInEl.value && checkOutEl.value) {
                debounceTimer = setTimeout(runSearch, 400);
            }
        });

        // Prefill from URL (?check_in=&check_out=&guests=)
        const q = new URLSearchParams(window.location.search);
        if (q.get('check_in')) checkInEl.value = q.get('check_in');
        if (q.get('check_out')) checkOutEl.value = q.get('check_out');
        if (q.get('guests')) guestsEl.value = q.get('guests');
        syncCheckoutMin();
        if (checkInEl.value && checkOutEl.value && checkOutEl.value > checkInEl.value) {
            runSearch();
        }

        // ── Booking request flow ──
        const bookingSection = document.getElementById('booking-section');
        const bookingForm = document.getElementById('booking-form');
        const bookingError = document.getElementById('booking-error');
        const bookingSuccess = document.getElementById('booking-success');
        const bookingSubmitBtn = document.getElementById('booking-submit');
        const roomNameEl = document.getElementById('booking-room-name');
        let selectedRoom = null;

        resultsEl.addEventListener('click', function (e) {
            const btn = e.target.closest('.request-btn');
            if (!btn) return;
            selectedRoom = lastResults.find(r => r.id === Number(btn.dataset.requestId));
            if (!selectedRoom) return;
            roomNameEl.textContent = selectedRoom.name + ' (' + selectedRoom.nights + ' night(s), ₦' + Number(selectedRoom.total).toLocaleString() + ')';
            document.getElementById('bk-guests-display').value = selectedRoom.guests + ' guest(s), ' + selectedRoom.check_in + ' → ' + selectedRoom.check_out;
            bookingSuccess.classList.add('hidden');
            bookingError.classList.add('hidden');
            bookingForm.classList.remove('hidden');
            bookingSection.classList.remove('hidden');
            bookingSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        document.getElementById('booking-cancel-btn').addEventListener('click', function () {
            bookingSection.classList.add('hidden');
            selectedRoom = null;
        });

        bookingForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!selectedRoom) return;
            bookingError.classList.add('hidden');

            const fd = new FormData(bookingForm);
            const payload = {
                room_type_id: selectedRoom.id,
                check_in: selectedRoom.check_in,
                check_out: selectedRoom.check_out,
                guests: selectedRoom.guests,
                guest_name: fd.get('guest_name'),
                guest_phone: fd.get('guest_phone'),
                guest_email: fd.get('guest_email') || null,
                special_requests: fd.get('special_requests') || null,
                coupon_code: fd.get('coupon_code') || null,
                ...window.collectAntiBot(this),
            };

            bookingSubmitBtn.disabled = true;
            const originalHtml = bookingSubmitBtn.innerHTML;
            bookingSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending…';

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
                    bookingForm.classList.add('hidden');
                    document.getElementById('booking-success-details').textContent =
                        'Reference ' + body.reference + ' — quoted total ₦' + Number(body.total).toLocaleString() +
                        '. We will contact you at ' + (payload.guest_phone || payload.guest_email) + ' shortly.';
                    const payBtn = document.getElementById('booking-pay-btn');
                    if (payBtn && body.payment_url) {
                        payBtn.href = body.payment_url;
                        payBtn.classList.remove('hidden');
                    }
                    bookingSuccess.classList.remove('hidden');
                    selectedRoom = null;
                } else {
                    throw new Error(body && body.message ? body.message : 'Could not submit your request. Please try again or contact us.');
                }
            } catch (err) {
                bookingError.textContent = err.message || 'Something went wrong. Please try again.';
                bookingError.classList.remove('hidden');
            } finally {
                bookingSubmitBtn.disabled = false;
                bookingSubmitBtn.innerHTML = originalHtml;
            }
        });
    })();
    </script>

@endsection