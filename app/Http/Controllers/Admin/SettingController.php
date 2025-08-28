<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; // Add this line
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::latest()->paginate(15);
        return view('admin.settings.index', compact('settings'));
    }

    public function create()
    {
        return view('admin.settings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:settings,key|max:255',
            'type' => 'required|in:text,image,video,file,number',
            'value_text' => 'nullable|string',
            'value_number' => 'nullable|numeric',
            'value_file' => 'nullable|file|max:10240',
        ]);

        $key = Str::slug($validated['key'], '_');
        $type = $validated['type'];
        $value = null;

        if ($type === 'text') {
            $value = $validated['value_text'];
        } elseif ($type === 'number') {
            $value = $validated['value_number'];
        } elseif ($request->hasFile('value_file')) {
            $value = $request->file('value_file')->store('settings', 'public');
        }

        Setting::create([
            'key' => $key,
            'type' => $type,
            'value' => $value,
        ]);

        Cache::forget('settings'); // Clear the cache

        return redirect()->route('admin.settings.index')->with('success', 'Setting created successfully.');
    }

    public function show(Setting $setting)
    {
        return view('admin.settings.show', compact('setting'));
    }

    public function edit(Setting $setting)
    {
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'type' => 'required|in:text,image,video,file,number',
            'value_text' => 'nullable|string',
            'value_number' => 'nullable|numeric',
            'value_file' => 'nullable|file|max:10240',
        ]);

        $setting->key = Str::slug($validated['key'], '_');
        $setting->type = $validated['type'];

        if ($setting->type === 'text') {
            $setting->value = $validated['value_text'];
        } elseif ($setting->type === 'number') {
            $setting->value = $validated['value_number'];
        } elseif ($request->hasFile('value_file')) {
            if ($setting->value) {
                Storage::disk('public')->delete($setting->value);
            }
            $setting->value = $request->file('value_file')->store('settings', 'public');
        }

        $setting->save();

        Cache::forget('settings'); // Clear the cache

        return redirect()->route('admin.settings.index')->with('success', 'Setting updated successfully.');
    }

    public function destroy(Setting $setting)
    {
        if (!in_array($setting->type, ['text', 'number']) && $setting->value) {
            Storage::disk('public')->delete($setting->value);
        }
        $setting->delete();

        Cache::forget('settings'); // Clear the cache

        return redirect()->route('admin.settings.index')->with('success', 'Setting deleted successfully.');
    }
}
