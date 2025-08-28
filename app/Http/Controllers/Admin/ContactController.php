<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the contact messages.
     */
    public function index()
    {
        $messages = Contact::latest()->paginate(15);
        return view('admin.contacts.index', compact('messages'));
    }

    /**
     * Display the specified contact message.
     */
    public function show(Contact $contact)
    {
        // Mark the message as read when it's viewed
        $contact->update(['is_read' => true]);

        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Remove the specified contact message from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')->with('success', 'Message deleted successfully.');
    }
}
