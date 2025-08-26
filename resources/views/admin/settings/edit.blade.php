@extends('layouts.admin')

@section('title', 'Edit Setting')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-6">Edit Setting</h2>
    <form action="{{ route('admin.settings.update', $setting) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label for="key" class="block text-sm font-medium text-gray-700">Key</label>
                <input type="text" name="key" id="key" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('key', $setting->key) }}" required>
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" id="type-selector" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="text" @if($setting->type === 'text') selected @endif>Text</option>
                    <option value="image" @if($setting->type === 'image') selected @endif>Image</option>
                    <option value="video" @if($setting->type === 'video') selected @endif>Video</option>
                    <option value="file" @if($setting->type === 'file') selected @endif>File (e.g., PDF)</option>
                </select>
            </div>
            <div id="value-text-field">
                <label for="value_text" class="block text-sm font-medium text-gray-700">Value</label>
                <textarea name="value_text" id="value_text" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('value_text', $setting->value) }}</textarea>
            </div>
            <div id="value-file-field" style="display: none;">
                <label for="value_file" class="block text-sm font-medium text-gray-700">New File (Optional)</label>
                <input type="file" name="value_file" id="value_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                 @if($setting->value)
                    <p class="mt-2 text-sm text-gray-500">Current: <a href="{{ asset('storage/' . $setting->value) }}" target="_blank" class="text-blue-500 hover:underline">View File</a></p>
                @endif
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Update Setting</button>
            <a href="{{ route('admin.settings.index') }}" class="text-gray-600 hover:text-gray-800 ml-4">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('type-selector').addEventListener('change', function() {
        const textField = document.getElementById('value-text-field');
        const fileField = document.getElementById('value-file-field');
        if (this.value === 'text') {
            textField.style.display = 'block';
            fileField.style.display = 'none';
        } else {
            textField.style.display = 'none';
            fileField.style.display = 'block';
        }
    });
    document.getElementById('type-selector').dispatchEvent(new Event('change'));
</script>
@endsection
