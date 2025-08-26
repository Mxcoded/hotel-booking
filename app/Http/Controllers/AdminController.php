<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        // We can pass stats to the dashboard later (e.g., room count, messages)
        return view('admin.dashboard');
    }
}
