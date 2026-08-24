@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="relative bg-gray-800 py-32 px-6 sm:py-40 sm:px-8 lg:px-12">
    <div class="absolute inset-0">
        <img src="https://placehold.co/1920x800/333333/FFFFFF?text=Our+Comfortable+Rooms" alt="Header showing a luxurious hotel room" class="h-full w-full object-cover">
        <div class="absolute inset-0 bg-gray-800 mix-blend-multiply" aria-hidden="true"></div>
    </div>
    <div class="relative mx-auto max-w-7xl text-center">
        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">Our Rooms & Suites</h1>
        <p class="mt-6 max-w-3xl mx-auto text-xl text-indigo-100">Find the perfect space for your stay. Each room type offers multiple units designed with your comfort in mind.</p>
    </div>
</div>

{{-- Availability Search --}}
<div class="bg-indigo-50 border-b border-indigo-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <form id="availability-form" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
            <div>
                <label for="avail-check-in" class="block text-sm font-semibold text-gray-700 mb-1">Check-in</label>
                <input type="date" id="avail-check-in" name="check_in" required
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="avail-check-out" class="block text-sm font-semibold text-gray-700 mb-1">Check-out</label>
                <input type="date" id="avail-check-out" name="check_out" required
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="avail-guests" class="block text-sm font-semibold text-gray-700 mb-1">Guests</label>
                <select id="avail-guests" name="guests"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @for ($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'Guest' : 'Guests' }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" id="avail-submit"
                    class="bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white font-bold py-3 px-6 rounded-lg transition duration-300 inline-flex items-center justify-center">
                <i class="fas fa-magnifying-glass mr-2"></i> Check Availability
            </button>
        </form>

        <p id="availability-error" class="hidden mt-3 text-sm font-medium text-red-600"></p>

        <div id="availability-results" class="mt-6 hidden space-y-4" aria-live="polite"></div>

        {{-- Inline booking request form --}}
        <div id="booking-section" class="mt-6 hidden">
            <div class="rounded-lg border border-green-200 bg-white p-5 shadow-sm">
                <h3 class="font-bold text-gray-900">Request Booking — <span id="booking-room-name" class="text-indigo-600"></span></h3>
                <p class="mt-1 text-sm text-gray-600">No payment needed now. Our team will confirm availability and get back to you.</p>
                <form id="booking-form" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <input type="text" name="honeypot" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                    <div>
                        <label for="bk-name" class="block text-sm font-semibold text-gray-700 mb-1">Full name *</label>
                        <input type="text" id="bk-name" name="guest_name" required maxlength="255"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="bk-phone" class="block text-sm font-semibold text-gray-700 mb-1">Phone / WhatsApp *</label>
                        <input type="tel" id="bk-phone" name="guest_phone" required maxlength="32"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="bk-email" class="block text-sm font-semibold text-gray-700 mb-1">Email (optional)</label>
                        <input type="email" id="bk-email" name="guest_email" maxlength="255"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="bk-guests-display" class="block text-sm font-semibold text-gray-700 mb-1">Guests</label>
                        <input type="text" id="bk-guests-display" disabled
                               class="w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm text-gray-600">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="bk-requests" class="block text-sm font-semibold text-gray-700 mb-1">Special requests (optional)</label>
                        <textarea id="bk-requests" name="special_requests" rows="2" maxlength="2000"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="sm:col-span-2 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <button type="submit" id="booking-submit"
                                class="bg-green-500 hover:bg-green-600 disabled:bg-green-300 text-white font-bold py-3 px-8 rounded-lg transition duration-300">
                            <i class="fas fa-paper-plane mr-2"></i>Send Request
                        </button>
                        <button type="button" id="booking-cancel-btn" class="text-sm font-semibold text-gray-500 hover:text-gray-700 underline">
                            Cancel
                        </button>
                        <span id="booking-error" class="hidden text-sm font-medium text-red-600"></span>
                    </div>
                </form>
                <div id="booking-success" class="hidden mt-2 rounded-lg bg-green-50 border border-green-200 p-4">
                    <p class="font-bold text-green-800"><i class="fas fa-check-circle mr-2"></i>Booking request received!</p>
                    <p id="booking-success-details" class="mt-1 text-sm text-green-700"></p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Rooms Listing Section --}}
<div class="bg-white">
    <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 class="sr-only">Our Room Types</h2>

        @if($roomTypes->count() > 0)
            <div class="grid grid-cols-1 gap-y-10 gap-x-6 lg:grid-cols-2 xl:gap-x-8">
                @php
                    $usd_rate = (float) setting('usd_exchange_rate', 0);
                @endphp

                @foreach($roomTypes as $roomType)
                    @php
                        $availableUnits = $roomType->activeUnits()->count();
                        $basePrice = $roomType->price;
                    @endphp
                    <div class="group relative rounded-lg border border-gray-200 p-4 sm:p-6 flex flex-col">
                        <!-- Favorite Button -->
                        <button onclick="toggleFavorite('roomtype-{{ $roomType->id }}')" class="favorite-btn absolute top-6 right-6 bg-white/80 rounded-full p-2 z-10 transition-transform duration-200 hover:scale-110" data-room-id="roomtype-{{ $roomType->id }}">
                            <i class="far fa-heart text-gray-700 text-xl"></i>
                        </button>
                        <div class="aspect-w-3 aspect-h-2 overflow-hidden rounded-lg bg-gray-200 group-hover:opacity-75">
                            <a href="{{ route('rooms.show', $roomType) }}">
                                @if($roomType->image)
                                    <img src="{{ thumbnail_url($roomType->image, 'card') }}" alt="{{ $roomType->name }}" class="h-full w-full object-cover object-center">
                                @else
                                    <img src="https://placehold.co/800x533/333333/FFFFFF?text={{ urlencode($roomType->name) }}" alt="{{ $roomType->name }}" class="h-full w-full object-cover object-center">
                                @endif
                            </a>
                        </div>
                        <div class="pt-6 pb-4 text-center flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">
                                    <a href="{{ route('rooms.show', $roomType) }}">
                                        {{ $roomType->name }}
                                    </a>
                                </h3>
                                <p class="mt-2 text-base text-gray-600">{{ $roomType->description }}</p>
                                <div class="mt-4 flex flex-wrap justify-center gap-x-4 gap-y-2 text-sm text-gray-500">
                                    @if(isset($roomType->features) && is_array($roomType->features))
                                        @foreach($roomType->features as $feature)
                                            @if(is_array($feature))
                                                <span><i class="fas {{ $feature['icon'] ?? '' }} mr-1"></i> {{ $feature['name'] ?? '' }}</span>
                                            @elseif(is_string($feature))
                                                <span><i class="fas fa-check mr-1"></i> {{ $feature }}</span>
                                            @endif
                                        @endforeach
                                    @endif
                                    <span><i class="fas fa-users mr-1"></i> {{ $roomType->base_guests }} Guest(s)</span>
                                    @if($roomType->max_guests && $roomType->max_guests > $roomType->base_guests)
                                        <span class="text-amber-600"><i class="fas fa-user-plus mr-1"></i> Up to {{ $roomType->max_guests }}</span>
                                    @endif
                                    @if($availableUnits > 1)
                                        <span class="text-green-600"><i class="fas fa-door-open mr-1"></i> {{ $availableUnits }} Units</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col items-center mt-4">
                            <div class="text-gray-600 mb-4">
                                <p class="font-bold text-xl text-gray-900">From ₦{{ number_format($basePrice, 2) }} / night</p>
                                @if($roomType->weekend_price)
                                    <p class="text-xs text-amber-600 mt-1"><i class="fas fa-calendar-day mr-1"></i>₦{{ number_format($roomType->weekend_price, 0) }} on Fri &amp; Sat</p>
                                @endif
                                @if($usd_rate > 0)
                                    <p class="text-sm">Approx. ${{ number_format($basePrice / $usd_rate, 2) }}</p>
                                @endif
                            </div>
                                <a href="https://wa.me/{{ setting('whatsapp_number', '+2348099999620') }}?text=Hi,%20I'm%20interested%20in%20booking%20the%20{{ urlencode($roomType->name) }}." target="_blank" class="whatsapp-link mt-auto w-full max-w-xs bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 inline-flex items-center justify-center">
                                    <i class="fab fa-whatsapp mr-2"></i> Reserve via WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-600 text-xl">We are currently updating our room listings. Please check back soon for our beautiful accommodations!</p>
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
        const card = () => '<div class="flex flex-col sm:flex-row sm:items-center gap-4 rounded-lg border border-indigo-100 bg-white p-4 shadow-sm">' +
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
            card.className = 'flex flex-col sm:flex-row sm:items-center gap-4 rounded-lg border border-indigo-100 bg-white p-4 shadow-sm transition-shadow hover:shadow-md';
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
                        ? '<button type="button" data-request-id="' + r.id + '" class="request-btn flex-1 text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm"><i class="fas fa-calendar-check mr-1"></i>Request</button>'
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
            honeypot: fd.get('honeypot') || '',
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
