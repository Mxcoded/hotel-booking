<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::latest()->paginate(10);
        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.rooms.create', ['features' => $this->features]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'guests' => 'required|integer|min:1',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'features' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        // Format the features array before saving
        if (!empty($validated['features'])) {
            $validated['features'] = $this->formatFeatures($validated['features']);
        }

        Room::create($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        return view('admin.rooms.edit', [
            'room' => $room,
            'features' => $this->features
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'guests' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'features' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        // Format the features array before saving
        $validated['features'] = !empty($validated['features']) ? $this->formatFeatures($validated['features']) : null;

        $room->update($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }
        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully.');
    }
    /**
     * Helper function to format features for database storage.
     */
    private function formatFeatures(array $selectedFeatures): array
    {
        $formatted = [];
        foreach ($selectedFeatures as $iconClass) {
            if (isset($this->features[$iconClass])) {
                $formatted[] = [
                    'icon' => $iconClass,
                    'name' => $this->features[$iconClass]
                ];
            }
        }
        return $formatted;
    }
}
