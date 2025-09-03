<?php

namespace App\Http\Controllers;

use App\Models\WhatsappLead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        // Check if a lead with this phone number already exists.
        // If not, create a new one. This prevents duplicate entries.
        WhatsappLead::firstOrCreate(
            ['phone' => $request->phone],
            [
                'name' => $request->name,
                'ip_address' => $request->ip()
            ]
        );

        return response()->json(['success' => true]);
    }
}
