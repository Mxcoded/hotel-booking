<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * The settings key used to store the food menu PDF path.
     */
    private const MENU_KEY = 'food_menu_pdf';

    /**
     * Display the menu management page.
     */
    public function index()
    {
        $menuSetting = Setting::where('key', self::MENU_KEY)->first();

        return view('admin.menu.index', compact('menuSetting'));
    }

    /**
     * Upload or replace the food menu PDF.
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_pdf' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        $menuSetting = Setting::where('key', self::MENU_KEY)->first();

        // Delete the old file from storage if one exists
        if ($menuSetting && $menuSetting->value) {
            Storage::disk('public')->delete($menuSetting->value);
        }

        $path = $request->file('menu_pdf')->store('menus', 'public');

        Setting::updateOrCreate(
            ['key' => self::MENU_KEY],
            ['value' => $path, 'type' => 'file']
        );

        Cache::forget('settings');

        return back()->with('success', 'Food menu PDF uploaded successfully.');
    }

    /**
     * Delete the current food menu PDF.
     */
    public function destroy()
    {
        $menuSetting = Setting::where('key', self::MENU_KEY)->first();

        if ($menuSetting) {
            if ($menuSetting->value) {
                Storage::disk('public')->delete($menuSetting->value);
            }
            $menuSetting->update(['value' => null]);
            Cache::forget('settings');
        }

        return back()->with('success', 'Food menu PDF removed successfully.');
    }
}
