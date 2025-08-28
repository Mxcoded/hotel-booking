@extends('layouts.admin')

@section('title', 'Create Setting')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-6">Create New Setting</h2>
    <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        @include('admin.settings._form')

        <div class="mt-6">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Save Setting</button>
            <a href="{{ route('admin.settings.index') }}" class="text-gray-600 hover:text-gray-800 ml-4">Cancel</a>
        </div>
    </form>
</div>
@endsection
