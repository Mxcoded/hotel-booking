<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Attraction Name</label>
        <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('name', $attraction->name ?? '') }}" required>
    </div>
    <div>
        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
        <input type="text" name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g., Food & Drink, Landmark, Shopping" value="{{ old('category', $attraction->category ?? '') }}" required>
    </div>
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('description', $attraction->description ?? '') }}</textarea>
    </div>
    <div>
        <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
        <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0" @if(request()->routeIs('admin.attractions.create')) required @endif>
        @isset($attraction->image)
            <img src="{{ asset('storage/' . $attraction->image) }}" alt="{{ $attraction->name }}" class="mt-4 h-32 w-auto object-cover rounded">
        @endisset
    </div>
</div>
