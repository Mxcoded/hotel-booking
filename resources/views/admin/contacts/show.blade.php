@extends('layouts.admin')

@section('title', 'View Message')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <div>
            <h2 class="text-2xl font-semibold">Message from: {{ $contact->name }}</h2>
            <p class="text-sm text-gray-500">Email: <a href="mailto:{{ $contact->email }}" class="text-blue-500">{{ $contact->email }}</a></p>
            <p class="text-sm text-gray-500">Received: {{ $contact->created_at->format('F j, Y, g:i a') }}</p>
        </div>
        <a href="{{ route('admin.contacts.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Inbox
        </a>
    </div>

    <div class="prose max-w-none">
        <p>{{ $contact->message }}</p>
    </div>
</div>
@endsection
