@extends('layouts.admin')

@section('title', 'Manage Rooms')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Rooms List</h2>
            <a href="{{ route('admin.rooms.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-plus mr-2"></i> Add New Room
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm text-left">Image</th>
                        <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm text-left">Name</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Price</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Guests</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($rooms as $room)
                        <tr>
                            <td class="py-3 px-4"><img src="{{ asset('storage/' . $room->image) }}" alt="{{ $room->name }}" class="h-16 w-24 object-cover rounded"></td>
                            <td class="py-3 px-4 font-semibold">{{ $room->name }}</td>
                            <td class="py-3 px-4">₦{{ number_format($room->price, 2) }}</td>
                            <td class="py-3 px-4">{{ $room->guests }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('admin.rooms.edit', $room) }}" class="text-blue-500 hover:text-blue-700 mr-4"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No rooms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         <div class="mt-6">
            {{ $rooms->links() }}
        </div>
    </div>
@endsection
