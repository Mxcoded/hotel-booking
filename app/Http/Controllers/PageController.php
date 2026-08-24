<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageReceived;
use App\Mail\ReservationRequestReceived;
use App\Models\Gallery;
use App\Models\Reservation;
use App\Models\RoomType;
use App\Models\RoomUnit;
use App\Models\RoomAvailability;
use App\Models\Setting;
use App\Models\Contact;
use App\Models\Feedback;
use App\Models\Attraction;

class PageController extends Controller
{
    /**
     * Display the home page.
     */
    public function home()
    {
        // Fetch featured room types (e.g., the first 3)
        $featuredRoomTypes = RoomType::featured()->ordered()->take(3)->get();

        // Fetch hero settings from the database
        $heroSetting = Setting::where('key', 'hero_media')->first();

        // Fetch the 3 latest approved testimonials with a rating of 4 or higher
        $testimonials = Feedback::where('is_approved', true)
            ->where('rating', '>=', 4)
            ->latest()
            ->take(6)
            ->get();


        $contactDetails = [
            'address' => '11 Adzope Crescent, off Kumasi Crescent, Wuse, Abuja 900288, Federal Capital Territory',
            'email' => 'reservations@brickspoint.ng',
            'phone' => '+234 809 999 9620'
        ];

        return view('welcome', [
            'featuredRoomTypes' => $featuredRoomTypes,
            'heroSetting' => $heroSetting,
            'address' => $contactDetails['address'],
            'email' => $contactDetails['email'],
            'phone' => $contactDetails['phone'],
            'testimonials' => $testimonials,
        ]);
    }

    /**
     * Display the rooms page.
     */
    public function rooms()
    {
        // Fetch all active room types with their available units
        $roomTypes = RoomType::active()->ordered()->with(['units' => fn ($q) => $q->active()->available()])->get();
        return view('rooms', compact('roomTypes'));
    }

    public function showRoom(RoomType $roomType)
    {
        // Load active units for this room type
        $roomType->load(['units' => fn ($q) => $q->active()->available()]);
        $roomType->load('media');
        return view('room-details', compact('roomType'));
    }

    /**
     * Display the gallery page.
     */
    public function gallery()
    {
        // Fetch all gallery images from the database
        $galleryImages = Gallery::latest()->get();
        return view('gallery', compact('galleryImages'));
    }

    /**
     * Handle the contact form submission.
     */
    public function storeContact(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
            'honeypot' => 'nullable|string|size:0',
        ]);

        // Store the message in the database
        $contact = Contact::create($validatedData);

        $this->notifyStaff(new ContactMessageReceived($contact), 'contact message');

        // Redirect back to the contact section with a success message
        return redirect('/#contact')->with('success', 'Thank you for your message! We will get back to you shortly.');
    }

    // New method for the favorites page view
    public function favorites()
    {
        return view('favorites');
    }

    // New method to fetch room type data for the favorites page via AJAX
    public function getFavoriteRooms(Request $request)
    {
        // Client-side favorites may be stored as "roomtype-{id}" strings; normalize them.
        $roomTypeIds = collect($request->input('ids', []))
            ->map(fn ($id) => (int) preg_replace('/\D/', '', (string) $id))
            ->filter()
            ->unique()
            ->take(50)
            ->values();

        if ($roomTypeIds->isEmpty()) {
            return response()->json([]);
        }

        $roomTypes = RoomType::query()
            ->active()
            ->whereIn('id', $roomTypeIds)
            ->get(['id', 'name', 'price', 'weekend_price', 'image']);

        return response()->json($roomTypes->map(fn (RoomType $roomType) => [
            'id' => $roomType->id,
            'name' => $roomType->name,
            'price' => (float) $roomType->price,
            'weekend_price' => $roomType->weekend_price ? (float) $roomType->weekend_price : null,
            'image_url' => $roomType->image ? thumbnail_url($roomType->image, 'thumb') : null,
        ]));
    }

    /**
     * Calculate a stay quote (nightly prices, fees, availability) for a room type.
     */
    public function stayQuote(Request $request)
    {
        $data = $request->validate([
            'room_type_id' => ['required', 'integer'],
            'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $roomType = RoomType::active()->find($data['room_type_id']);

        if (!$roomType) {
            return response()->json(['message' => 'Room type not found.'], 404);
        }

        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $nights = (int) $checkIn->diffInDays($checkOut);
        $guests = (int) ($data['guests'] ?? $roomType->base_guests);

        $errors = [];

        if ($nights < (int) $roomType->min_stay) {
            $errors[] = "Minimum stay for this room is {$roomType->min_stay} night(s).";
        }

        if ($roomType->max_stay && $nights > (int) $roomType->max_stay) {
            $errors[] = "Maximum stay for this room is {$roomType->max_stay} night(s).";
        }

        if ($guests > $roomType->getMaxOccupancy()) {
            $errors[] = "This room accommodates up to {$roomType->getMaxOccupancy()} guest(s).";
        }

        $nightly = [];
        for ($date = $checkIn->copy(); $date->lt($checkOut); $date->addDay()) {
            $nightly[] = [
                'date' => $date->toDateString(),
                'price' => (float) $roomType->getPriceForDate($date->toDateString()),
            ];
        }

        $subtotal = round(array_sum(array_column($nightly, 'price')), 2);
        $extraGuestFee = (float) $roomType->calculateExtraGuestFee($guests);

        if ($roomType->getTotalUnitsCount() === 0) {
            $availability = 'contact_us';
        } else {
            $availability = $roomType->isAvailableForRange(
                $data['check_in'],
                $data['check_out'],
                RoomAvailability::indexForRange($data['check_in'], $checkOut->copy()->subDay()->toDateString())
            )
                ? 'available'
                : 'unavailable';
        }

        return response()->json([
            'room_type' => [
                'id' => $roomType->id,
                'name' => $roomType->name,
            ],
            'nights' => $nights,
            'nightly' => $nightly,
            'guests' => $guests,
            'subtotal' => $subtotal,
            'extra_guest_fee' => $extraGuestFee,
            'total' => round($subtotal + $extraGuestFee, 2),
            'min_stay' => (int) $roomType->min_stay,
            'max_guests' => $roomType->getMaxOccupancy(),
            'availability' => $availability,
            'errors' => $errors,
        ]);
    }

    /**
     * Search availability across all room types for a date range.
     */
    public function availabilitySearch(Request $request)
    {
        $data = $request->validate([
            'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $nights = (int) $checkIn->diffInDays($checkOut);
        $guests = (int) ($data['guests'] ?? 1);

        // One query for every unit's overrides in the window — no per-night lookups.
        $overridesIndex = RoomAvailability::indexForRange(
            $data['check_in'],
            $checkOut->copy()->subDay()->toDateString()
        );

        // One grouped count instead of a COUNT per room type.
        $unitCounts = RoomUnit::query()
            ->active()
            ->selectRaw('room_type_id, count(*) as units_count')
            ->groupBy('room_type_id')
            ->pluck('units_count', 'room_type_id');

        $results = RoomType::active()->ordered()->with('activeUnits')->get()
            ->filter(fn (RoomType $roomType) => $roomType->getMaxOccupancy() >= $guests)
            ->map(function (RoomType $roomType) use ($data, $nights, $guests, $overridesIndex, $unitCounts) {
                $subtotal = 0.0;
                for ($date = Carbon::parse($data['check_in']); $date->lt(Carbon::parse($data['check_out'])); $date->addDay()) {
                    $subtotal += $roomType->getPriceForDate($date->toDateString());
                }
                $subtotal = round($subtotal, 2);
                $extraGuestFee = (float) $roomType->calculateExtraGuestFee($guests);

                $totalUnits = (int) ($unitCounts[$roomType->id] ?? 0);
                if ($totalUnits === 0) {
                    $status = 'contact_us';
                    $unitsFree = 0;
                } else {
                    $unitsFree = $roomType->activeUnits->filter(
                        fn ($unit) => $unit->isAvailableForRange($data['check_in'], $data['check_out'], $overridesIndex)
                    )->count();
                    $status = $unitsFree > 0 ? 'available' : 'unavailable';
                }

                $errors = [];
                if ($nights < (int) $roomType->min_stay) {
                    $errors[] = "Minimum stay is {$roomType->min_stay} night(s).";
                }
                if ($roomType->max_stay && $nights > (int) $roomType->max_stay) {
                    $errors[] = "Maximum stay is {$roomType->max_stay} night(s).";
                }

                return [
                    'id' => $roomType->id,
                    'name' => $roomType->name,
                    'slug' => $roomType->slug,
                    'url' => route('rooms.show', $roomType),
                    'image_url' => $roomType->image ? thumbnail_url($roomType->image, 'card') : null,
                    'max_guests' => $roomType->getMaxOccupancy(),
                    'base_guests' => (int) $roomType->base_guests,
                    'min_stay' => (int) $roomType->min_stay,
                    'nights' => $nights,
                    'guests' => $guests,
                    'check_in' => $data['check_in'],
                    'check_out' => $data['check_out'],
                    'subtotal' => $subtotal,
                    'extra_guest_fee' => $extraGuestFee,
                    'total' => round($subtotal + $extraGuestFee, 2),
                    'units_free' => $unitsFree,
                    'status' => $status,
                    'errors' => $errors,
                ];
            })
            ->values()
            ->sortBy([['status', 'asc'], ['total', 'asc']])
            ->values();

        return response()->json([
            'check_in' => $data['check_in'],
            'check_out' => $data['check_out'],
            'nights' => $nights,
            'guests' => $guests,
            'results' => $results,
        ]);
    }

    /**
     * Store a booking request submitted from the availability widget.
     * Price and availability are always recomputed server-side.
     */
    public function storeReservation(Request $request)
    {
        $data = $request->validate([
            'room_type_id' => ['required', 'integer'],
            'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:20'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:32'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
            'honeypot' => ['nullable', 'string', 'size:0'],
        ]);

        $data['guest_email'] = $data['guest_email'] ?? null;

        $roomType = RoomType::active()->find($data['room_type_id']);

        if (!$roomType) {
            return response()->json(['message' => 'Room type not found.'], 404);
        }

        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $nights = (int) $checkIn->diffInDays($checkOut);
        $guests = (int) $data['guests'];

        if ($nights < (int) $roomType->min_stay || ($roomType->max_stay && $nights > (int) $roomType->max_stay)) {
            return response()->json([
                'message' => "This room requires a stay of {$roomType->min_stay}"
                    . ($roomType->max_stay ? " to {$roomType->max_stay}" : '+') . ' night(s).',
            ], 422);
        }

        if ($guests > $roomType->getMaxOccupancy()) {
            return response()->json([
                'message' => "This room accommodates up to {$roomType->getMaxOccupancy()} guest(s).",
            ], 422);
        }

        $totalUnits = $roomType->getTotalUnitsCount();

        if ($totalUnits === 0) {
            return response()->json([
                'message' => 'This room cannot be booked online at the moment. Please contact us directly.',
            ], 422);
        }

        $unitsFree = $roomType->activeUnits()->get()
            ->filter(fn ($unit) => $unit->isAvailableForRange($data['check_in'], $data['check_out']))
            ->count();

        if ($unitsFree === 0) {
            return response()->json([
                'message' => 'Unfortunately this room is no longer available for those dates. Please try different dates or contact us.',
            ], 422);
        }

        $subtotal = 0.0;
        for ($date = $checkIn->copy(); $date->lt($checkOut); $date->addDay()) {
            $subtotal += $roomType->getPriceForDate($date->toDateString());
        }

        $reservation = Reservation::create([
            'room_type_id' => $roomType->id,
            'guest_name' => $data['guest_name'],
            'guest_email' => $data['guest_email'],
            'guest_phone' => $data['guest_phone'],
            'check_in' => $data['check_in'],
            'check_out' => $data['check_out'],
            'guests' => $guests,
            'quoted_total' => round($subtotal + (float) $roomType->calculateExtraGuestFee($guests), 2),
            'status' => Reservation::STATUS_PENDING,
            'special_requests' => $data['special_requests'] ?? null,
            'source' => 'website',
        ]);

        $this->notifyStaff(new ReservationRequestReceived($reservation), 'reservation request');

        return response()->json([
            'success' => true,
            'message' => 'Booking request received! Our team will confirm shortly.',
            'reference' => 'BR-' . str_pad((string) $reservation->id, 5, '0', STR_PAD_LEFT),
            'total' => (float) $reservation->quoted_total,
        ], 201);
    }

    public function localGuide()
    {
        $attractions = Attraction::latest()->get();
        $categories = $attractions->pluck('category')->unique();
        return view('local-guide', compact('attractions', 'categories'));
    }

    /**
     * Display the public food menu page.
     */
    public function menu()
    {
        $menuPdf = setting('food_menu_pdf');
        return view('menu', compact('menuPdf'));
    }

    /**
     * Generate the XML sitemap for search engines.
     */
    public function sitemap()
    {
        $staticPages = collect([
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('rooms'), 'priority' => '0.9'],
            ['loc' => route('gallery'), 'priority' => '0.6'],
            ['loc' => route('local-guide'), 'priority' => '0.5'],
            ['loc' => route('menu'), 'priority' => '0.5'],
        ]);

        $roomTypes = RoomType::active()->ordered()->get(['id', 'slug', 'updated_at'])
            ->map(fn (RoomType $roomType) => [
                'loc' => route('rooms.show', $roomType),
                'lastmod' => optional($roomType->updated_at)->toAtomString(),
                'priority' => '0.8',
            ]);

        return response()
            ->view('sitemap', ['urls' => $staticPages->merge($roomTypes)])
            ->header('Content-Type', 'application/xml');
    }
}