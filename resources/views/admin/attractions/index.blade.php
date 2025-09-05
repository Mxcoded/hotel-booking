@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Manage Local Attractions</h1>
    <a href="{{ route('admin.attractions.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Add New Attraction</a>
</div>

@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
        <p>{{ session('success') }}</p>
    </div>
@endif

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Image</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($attractions as $attraction)
            <tr>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                    <img src="{{ asset('storage/' . $attraction->image) }}" alt="{{ $attraction->name }}" class="w-24 h-16 object-cover rounded">
                </td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                    <p class="text-gray-900 whitespace-no-wrap">{{ $attraction->name }}</p>
                </td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                    <p class="text-gray-900 whitespace-no-wrap">{{ $attraction->category }}</p>
                </td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                    <a href="{{ route('admin.attractions.edit', $attraction) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                    <form action="{{ route('admin.attractions.destroy', $attraction) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure you want to delete this attraction?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-10 text-gray-500">No attractions have been added yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
