<div class="space-y-4">
    <div>
        <label for="key" class="block text-sm font-medium text-gray-700">Key (e.g., phone_number)</label>
        <input type="text" name="key" id="key" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('key', $setting->key ?? '') }}" required>
    </div>
    <div>
        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
        <select name="type" id="type-selector" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="text" @if(old('type', $setting->type ?? '') === 'text') selected @endif>Text</option>
            <option value="number" @if(old('type', $setting->type ?? '') === 'number') selected @endif>Number</option>
            <option value="image" @if(old('type', $setting->type ?? '') === 'image') selected @endif>Image</option>
            <option value="video" @if(old('type', $setting->type ?? '') === 'video') selected @endif>Video</option>
            <option value="file" @if(old('type', $setting->type ?? '') === 'file') selected @endif>File (e.g., PDF)</option>
        </select>
    </div>
    <div id="value-text-field" style="display: none;">
        <label for="value_text" class="block text-sm font-medium text-gray-700">Value</label>
        <textarea name="value_text" id="value_text" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('value_text', $setting->value ?? '') }}</textarea>
    </div>
    <div id="value-number-field" style="display: none;">
        <label for="value_number" class="block text-sm font-medium text-gray-700">Value</label>
        <input type="number" name="value_number" id="value_number" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('value_number', $setting->value ?? '') }}">
    </div>
    <div id="value-file-field" style="display: none;">
        <label for="value_file" class="block text-sm font-medium text-gray-700">File</label>
        <input type="file" name="value_file" id="value_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        @if(isset($setting) && $setting->value && in_array($setting->type, ['image', 'video', 'file']))
            <p class="mt-2 text-sm text-gray-500">Current: <a href="{{ asset('storage/' . $setting->value) }}" target="_blank" class="text-blue-500 hover:underline">View File</a></p>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelector = document.getElementById('type-selector');
        const textField = document.getElementById('value-text-field');
        const numberField = document.getElementById('value-number-field');
        const fileField = document.getElementById('value-file-field');

        function toggleFields() {
            textField.style.display = 'none';
            numberField.style.display = 'none';
            fileField.style.display = 'none';

            if (typeSelector.value === 'text') {
                textField.style.display = 'block';
            } else if (typeSelector.value === 'number') {
                numberField.style.display = 'block';
            } else {
                fileField.style.display = 'block';
            }
        }

        typeSelector.addEventListener('change', toggleFields);
        toggleFields(); // Run on page load
    });
</script>
