<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Gallery;
use App\Models\RoomType;
use App\Models\RoomUnit;
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
        Contact::create($validatedData);

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
        $roomTypeIds = $request->input('ids', []);

        if (empty($roomTypeIds)) {
            return response()->json([]);
        }

        $roomTypes = RoomType::whereIn('id', $roomTypeIds)->get();

        return response()->json($roomTypes);
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
}