<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Gallery;
use App\Models\Room;
use App\Models\Setting;

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

        $contactDetails = [
            'address' => '123 Luxury Lane, Aso, Nigeria',
            'email' => 'reservations@brickspoint.com',
            'phone' => '+234 809 999 9620'
        ];

        return view('welcome', [
            'featuredRooms' => $featuredRooms,
            'heroSetting' => $heroSetting,
            'address' => $contactDetails['address'],
            'email' => $contactDetails['email'],
            'phone' => $contactDetails['phone'],
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

        // For this example, we'll just log the data.
        Log::info('New contact form submission:', $validatedData);

        // Redirect back to the contact section with a success message
        return redirect('/#contact')->with('success', 'Thank you for your message! We will get back to you shortly.');
    }
}
