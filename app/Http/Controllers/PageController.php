<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Gallery;
use App\Models\Room;
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
        // Fetch featured rooms (e.g., the first 3)
        $featuredRooms = Room::take(3)->get();

        // Fetch hero settings from the database
        $heroSetting = Setting::where('key', 'hero_media')->first();

        // Fetch the 3 latest approved testimonials with a rating of 4 or higher
        $testimonials = Feedback::where('is_approved', true)
            ->where('rating', '>=', 4)
            ->latest()
            ->take(3)
            ->get();


        $contactDetails = [
            'address' => '11 Adzope Crescent, off Kumasi Crescent, Wuse, Abuja 900288, Federal Capital Territory',
            'email' => 'reservations@brickspoint.ng',
            'phone' => '+234 809 999 9620'
        ];

        return view('welcome', [
            'featuredRooms' => $featuredRooms,
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
        // Fetch all rooms from the database
        $rooms = Room::all();
        return view('rooms', compact('rooms'));
    }
    public function showRoom(Room $room)
    {
        // Eager load the media files for the room
        $room->load('media');
        return view('room-details', compact('room'));
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

    // New method to fetch room data for the favorites page via AJAX
    public function getFavoriteRooms(Request $request)
    {
        $roomIds = $request->input('ids', []);

        if (empty($roomIds)) {
            return response()->json([]);
        }

        $rooms = Room::whereIn('id', $roomIds)->get();

        return response()->json($rooms);
    }
    public function localGuide()
    {
        $attractions = Attraction::latest()->get();
        $categories = $attractions->pluck('category')->unique();
        return view('local-guide', compact('attractions', 'categories'));
    }
}
