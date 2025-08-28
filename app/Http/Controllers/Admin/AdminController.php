<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Gallery;
use App\Models\Room;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with dynamic stats.
     */
    public function dashboard()
    {
        $roomCount = Room::count();
        $galleryCount = Gallery::count();
        $unreadMessagesCount = Contact::where('is_read', false)->count();

        return view('admin.dashboard', compact('roomCount', 'galleryCount', 'unreadMessagesCount'));
    }
}
