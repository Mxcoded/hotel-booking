@extends('layouts.admin')

@section('title', 'View Setting')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">View Setting</h2>
        <div>
            <a href="{{ route('admin.settings.edit', $setting) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg mr-2">Edit</a>
            <a href="{{ route('admin.settings.index') }}" class="text-gray-600 hover:text-gray-800">Back to List</a>
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <h3 class="text-sm font-medium text-gray-500">Key</h3>
            <p class="mt-1 text-lg text-gray-900 font-mono">{{ $setting->key }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500">Type</h3>
            <p class="mt-1 text-lg text-gray-900">{{ $setting->type }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500">Value</h3>
            <div class="mt-1">
                @if($setting->type === 'image')
                    <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->key }}" class="max-w-xs rounded-lg border">
                @elseif($setting->type === 'video')
                    <video src="{{ asset('storage/' . $setting->value) }}" class="max-w-xs rounded-lg border" controls></video>
                @elseif($setting->type === 'file')
                     <a href="{{ asset('storage/' . $setting->value) }}" target="_blank" class="text-blue-500 hover:underline">Download/View File</a>
                @else
                    <div class="p-4 bg-gray-100 rounded-md text-gray-800 whitespace-pre-wrap">{{ $setting->value }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
