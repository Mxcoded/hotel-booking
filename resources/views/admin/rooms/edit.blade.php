@extends('layouts.admin')

@section('title', 'Edit Room')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Edit Room: {{ $room->name }}</h2>

        <form action="{{ route('admin.rooms.update', $room) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            {{-- Include the reusable form partial --}}
            @include('admin.rooms._form', ['room' => $room])

            <div class="mt-6 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Update Room</button>
                <a href="{{ route('admin.rooms.index') }}" class="ml-4 text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Section for Managing Room Media --}}
    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-4">Manage Room Media (Images & Videos)</h3>

        {{-- Upload Form --}}
        <div class="mb-6 border-b pb-6">
            <form action="{{ route('admin.rooms.media.store', $room) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div>
                    <label for="media" class="block mb-2 text-sm font-medium text-gray-900">Upload New Media (you can select multiple files)</label>
                    <input type="file" name="media[]" id="media" multiple class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    <p class="mt-1 text-sm text-gray-500">Allowed: JPG, PNG, GIF, MP4, MOV. Max size: 20MB per file.</p>
                </div>
                <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Upload Media</button>
            </form>
        </div>

        {{-- Existing Media Gallery --}}
        <div>
            <h4 class="text-lg font-semibold mb-4">Existing Media</h4>
            @if ($room->media->isEmpty())
                <p class="text-gray-500">No additional media has been uploaded for this room.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($room->media as $media)
                        <div class="relative group">
                            @if ($media->type === 'image')
                                <img src="{{ asset('storage/' . $media->file_path) }}" alt="Room media" class="rounded-lg object-cover w-full h-32">
                            @else
                                <div class="w-full h-32 bg-gray-800 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-video text-white text-4xl"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('admin.rooms.media.destroy', $media) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white text-2xl" onclick="return confirm('Are you sure you want to delete this item?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

