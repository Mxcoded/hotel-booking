@extends('layouts.admin')

@section('title', 'View Feedback')

@section('content')
<div class="bg-white p-6 md:p-8 shadow-md rounded-lg">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">View Feedback</h1>
        <a href="{{ route('admin.feedback.index') }}" class="text-indigo-600 hover:text-indigo-800">&larr; Back to all feedback</a>
    </div>

    <div class="border-t border-gray-200">
        <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">From</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $feedback->name ?? 'Anonymous' }} &lt;{{ $feedback->email ?? 'No Email' }}&gt;</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Received</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $feedback->created_at->format('F j, Y, g:i a') }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Rating</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                        @endfor
                        <span class="ml-2">({{ $feedback->rating }} / 5)</span>
                    </div>
                </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Message</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm-col-span-2 whitespace-pre-wrap">{{ $feedback->message }}</dd>
            </div>
        </dl>
    </div>
    <div class="mt-6 flex items-center justify-between">
        <form action="{{ route('admin.feedback.toggleApproval', $feedback) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white {{ $feedback->is_approved ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }}">
                {{ $feedback->is_approved ? 'Unapprove' : 'Approve for Testimonials' }}
            </button>
        </form>

        <form action="{{ route('admin.feedback.destroy', $feedback) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this feedback?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-900">Delete Feedback</button>
        </form>
    </div>
</div>
@endsection
