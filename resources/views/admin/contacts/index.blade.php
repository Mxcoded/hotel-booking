@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-6">Inbox</h2>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">From</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Message</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Received</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($messages as $message)
                    <tr class="@if(!$message->is_read) bg-blue-50 font-bold @endif">
                        <td class="py-3 px-4">{{ $message->name }}<br><span class="text-xs font-normal text-gray-500">{{ $message->email }}</span></td>
                        <td class="py-3 px-4">{{ Str::limit($message->message, 70) }}</td>
                        <td class="py-3 px-4 text-sm">{{ $message->created_at->format('d M Y, h:i A') }}</td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('admin.contacts.show', $message) }}" class="text-blue-500 hover:text-blue-700 mr-4"><i class="fas fa-eye"></i> View</a>
                            <form action="{{ route('admin.contacts.destroy', $message) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">You have no messages.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
     <div class="mt-6">
        {{ $messages->links() }}
    </div>
</div>
@endsection
