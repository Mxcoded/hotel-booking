<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display the gallery management page.
     */
    public function index()
    {
        $images = Gallery::latest()->get();
        return view('admin.gallery.index', compact('images'));
    }

    /**
     * Store a newly uploaded image.
     */
    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120' // Max 5MB each
        ]);

        foreach ($request->file('images') as $image) {
            $path = $image->store('gallery', 'public');
            Gallery::create([
                'path' => $path,
                'alt_text' => 'Brickspoint Hotel Gallery Image' // You can add a field for this later if needed
            ]);
        }

        return back()->with('success', 'Images uploaded successfully.');
    }

    /**
     * Remove the specified image from storage.
     */
    public function destroy(Gallery $gallery)
    {
        Storage::disk('public')->delete($gallery->path);
        $gallery->delete();

        return back()->with('success', 'Image deleted successfully.');
    }
}
