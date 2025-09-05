@extends('layouts.admin')

@section('title', 'Guest Feedback')

@section('content')
<div class="bg-white p-6 md:p-8 shadow-md rounded-lg">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Guest Feedback</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Rating</th>
                    <th scope="col" class="px-6 py-3">Message</th>
                    <th scope="col" class="px-6 py-3">From</th>
                    <th scope="col" class="px-6 py-3">Received</th>
                    <th scope="col" class="px-6 py-3">Approved</th>
                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feedbacks as $feedback)
                    <tr class="bg-white border-b hover:bg-gray-50 {{ !$feedback->is_read ? 'font-bold' : '' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ Str::limit($feedback->message, 50) }}</td>
                        <td class="px-6 py-4">{{ $feedback->name ?? 'Anonymous' }}</td>
                        <td class="px-6 py-4">{{ $feedback->created_at->format('d M, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $feedback->is_approved ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $feedback->is_approved ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <a href="{{ route('admin.feedback.show', $feedback) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No feedback has been submitted yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $feedbacks->links() }}
    </div>
</div>
@endsection
