<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::latest()->paginate(15);
        $unreadFeedbackCount = Feedback::where('is_read', false)->count(); //count the unread feedback
        return view('admin.feedback.index', compact('feedbacks','unreadFeedbackCount'));
    }

    public function show(Feedback $feedback)
    {
        // Mark as read when viewed
        $feedback->update(['is_read' => true]);
        $unreadFeedbackCount = Feedback::where('is_read', false)->count(); //count the unread feedback
        return view('admin.feedback.show', compact('feedback', 'unreadFeedbackCount'));
    }
    
    public function toggleApproval(Feedback $feedback)
    {
        $feedback->update(['is_approved' => !$feedback->is_approved]);
        return back()->with('success', 'Feedback approval status updated.');
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        return redirect()->route('admin.feedback.index')->with('success', 'Feedback deleted successfully.');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'required|string',
        ]);

        Feedback::create($validated);

        return response()->json(['success' => true, 'message' => 'Feedback submitted successfully!']);
    }
}
