<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    // Define a master list of available features
    private $features = [
        'fa-wifi' => 'Free WiFi',
        'fa-bath' => 'Bathtub',
        'fa-tv' => 'Cable TV',
        'fa-snowflake' => 'Air Conditioning',
        'fa-mountain' => 'City View',
        'fa-coffee' => 'Coffee Maker',
    ];
    public function index()
    {
        $rooms = Room::latest()->paginate(10);
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        // Pass the master 'features' list to the create view
        return view('admin.rooms.create')->with('features', $this->getFeaturesList());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'guests' => 'required|integer|min:1',
            'image' => 'required|image|max:5120', // Max 5MB
            'features' => 'nullable|array'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        $validated['features'] = $this->formatFeatures($request->input('features'));

        Room::create($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully.');
    }

    public function edit(Room $room)
    {
        $room->load('media');
        // Pass both the specific room and the master 'features' list to the edit view
        return view('admin.rooms.edit', compact('room'))->with('features', $this->getFeaturesList());
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'guests' => 'required|integer|min:1',
            'image' => 'nullable|image|max:5120',
            'features' => 'nullable|array'
        ]);

        if ($request->hasFile('image')) {
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        $validated['features'] = $this->formatFeatures($request->input('features'));

        $room->update($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }
        foreach ($room->media as $media) {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();
        }
        $room->delete();
        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully.');
    }

    public function storeMedia(Request $request, Room $room)
    {
        $request->validate([
            'media.*' => 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt|max:5120', // Max 5MB per file
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('room_media', 'public');
                $type = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
                $room->media()->create(['file_path' => $path, 'type' => $type]);
            }
        }
        return back()->with('success', 'Media uploaded successfully.');
    }

    public function destroyMedia(RoomMedia $media)
    {
        Storage::disk('public')->delete($media->file_path);
        $media->delete();
        return back()->with('success', 'Media deleted successfully.');
    }

    private function getFeaturesList()
    {
        // The key is the value we store (e.g., 'fa-wifi')
        // The value is the display name (e.g., 'Free Wi-Fi')
        return [
            'fa-wifi' => 'Free Wi-Fi',
            'fa-bath' => 'Bathtub',
            'fa-tv' => 'Flat Screen TV',
            'fa-snowflake' => 'Air Conditioning',
            'fa-utensils' => 'Kitchenette',
        ];
    }

    private function formatFeatures($featuresArray)
    {
        if (empty($featuresArray)) {
            return null;
        }
        $masterList = $this->getFeaturesList();
        // Map the submitted keys (e.g., ['fa-wifi', 'fa-tv']) to the full format
        return collect($featuresArray)->map(function ($featureKey) use ($masterList) {
            return [
                'name' => $masterList[$featureKey] ?? 'Unknown',
                'icon' => $featureKey
            ];
        })->values()->all();
    }
}
