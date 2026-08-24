<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackSubmitted;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    /**
     * Show the public feedback form page.
     */
    public function create()
    {
        return view('feedback');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'required|string',
            'honeypot' => 'nullable|string|size:0',
        ]);

        $feedback = Feedback::create($validated);

        $this->notifyStaff(new FeedbackSubmitted($feedback), 'feedback');

        $message = 'Feedback submitted successfully!';

        // This checks if the request was an AJAX (fetch) request
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        // This is the fallback for a standard form post (e.g., JS disabled)
        // It redirects back to the feedback page with a success message.
        return redirect()->route('feedback.create')->with('success', $message);
    }
}
