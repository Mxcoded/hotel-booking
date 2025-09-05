<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttractionController extends Controller
{
    public function index()
    {
        $attractions = Attraction::latest()->paginate(10);
        $unreadFeedbackCount = Feedback::where('is_read', false)->count();
        return view('admin.attractions.index', compact('attractions', 'unreadFeedbackCount'));
    }

    public function create()
    {
        $unreadFeedbackCount = Feedback::where('is_read', false)->count();
        return view('admin.attractions.create', compact('unreadFeedbackCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|max:2048',
        ]);

        $validated['image'] = $request->file('image')->store('attractions', 'public');

        Attraction::create($validated);

        return redirect()->route('admin.attractions.index')->with('success', 'Attraction created successfully.');
    }

    public function edit(Attraction $attraction)
    {
        return view('admin.attractions.edit', compact('attraction'));
    }

    public function update(Request $request, Attraction $attraction)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($attraction->image);
            $validated['image'] = $request->file('image')->store('attractions', 'public');
        }

        $attraction->update($validated);

        return redirect()->route('admin.attractions.index')->with('success', 'Attraction updated successfully.');
    }

    public function destroy(Attraction $attraction)
    {
        Storage::disk('public')->delete($attraction->image);
        $attraction->delete();

        return redirect()->route('admin.attractions.index')->with('success', 'Attraction deleted successfully.');
    }
}
