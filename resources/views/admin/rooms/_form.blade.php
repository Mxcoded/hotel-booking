<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Name -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Room Name</label>
        <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('name', $room->name ?? '') }}" required>
    </div>

    <!-- Price -->
    <div>
        <label for="price" class="block text-sm font-medium text-gray-700">Price per Night (₦)</label>
        <input type="number" name="price" id="price" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('price', $room->price ?? '') }}" required>
    </div>

    <!-- Guests -->
    <div>
        <label for="guests" class="block text-sm font-medium text-gray-700">Max Guests</label>
        <input type="number" name="guests" id="guests" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('guests', $room->guests ?? '') }}" required>
    </div>

    <!-- Main Image -->
    <div>
        <label for="image" class="block text-sm font-medium text-gray-700">Main Room Image @if(!isset($room)) (Required) @else (Optional: to change) @endif</label>
        <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" @if(!isset($room)) required @endif>
        @isset($room->image)
            <img src="{{ asset('storage/' . $room->image) }}" alt="{{ $room->name }}" class="mt-4 h-24 w-32 object-cover rounded">
        @endisset
    </div>
</div>

<!-- Description -->
<div class="mt-6">
    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
    <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('description', $room->description ?? '') }}</textarea>
</div>

<!-- Features -->
<div class="mt-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Room Features</label>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @php
            // Get an array of the room's current feature icon classes for easy checking
            $currentFeatures = isset($room) && is_array($room->features) ? array_column($room->features, 'icon') : [];
        @endphp
        @foreach($features as $featureKey => $featureName)
            <label class="flex items-center space-x-3">
                <input type="checkbox" name="features[]" value="{{ $featureKey }}"
                       @if(in_array($featureKey, old('features', $currentFeatures))) checked @endif
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span><i class="fas {{ $featureKey }} mr-2 text-gray-600"></i>{{ $featureName }}</span>
            </label>
        @endforeach
    </div>
</div>
