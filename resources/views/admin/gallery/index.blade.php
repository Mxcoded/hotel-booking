@extends('layouts.admin')

@section('title', 'Manage Gallery')

@section('content')
    <!-- Upload Form -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-semibold mb-4">Upload New Images</h2>
        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700">Select Images (you can select multiple)</label>
                <input type="file" name="images[]" id="images" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" multiple required>
            </div>
            @error('images.*')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
            <div class="mt-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                    <i class="fas fa-upload mr-2"></i> Upload Images
                </button>
            </div>
        </form>
    </div>

    <!-- Image Grid -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-6">Existing Images</h2>
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($images->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($images as $image)
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $image->path) }}" alt="Gallery Image" class="w-full h-32 object-cover rounded-lg">
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <form action="{{ route('admin.gallery.destroy', $image) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-white text-2xl hover:text-red-500">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-600">No images have been uploaded yet.</p>
        @endif
    </div>
@endsection
