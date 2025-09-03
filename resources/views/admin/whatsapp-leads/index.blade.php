@extends('layouts.admin')

@section('title', 'WhatsApp Leads')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">WhatsApp Leads</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-6 text-left">Name</th>
                    <th class="py-3 px-6 text-left">Phone</th>
                    <th class="py-3 px-6 text-left">
                        <a href="{{ route('admin.whatsapp-leads.index', ['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Date Logged
                        </a>
                    </th>
                    <th class="py-3 px-6 text-left">
                        <a href="{{ route('admin.whatsapp-leads.index', ['sort' => 'last_seen_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Last Website Visit
                        </a>
                    </th>
                    <th class="py-3 px-6 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse ($leads as $lead)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6">{{ $lead->name }}</td>
                        <td class="py-3 px-6">{{ $lead->phone }}</td>
                        <td class="py-3 px-6">{{ $lead->created_at->format('d M Y, h:i A') }}</td>
                        <td class="py-3 px-6">{{ $lead->last_seen_at ? \Carbon\Carbon::parse($lead->last_seen_at)->diffForHumans() : 'N/A' }}</td>
                        <td class="py-3 px-6">
                            <form action="{{ route('admin.whatsapp-leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lead?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 px-6 text-center">No leads found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $leads->links() }}
    </div>
</div>
@endsection
