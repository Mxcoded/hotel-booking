@extends('layouts.admin')

@section('content')
<h1 class="text-3xl font-bold mb-6">Add New Attraction</h1>

<div class="bg-white p-8 rounded-lg shadow-md">
    <form action="{{ route('admin.attractions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.attractions._form')
        <div class="mt-8">
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Save Attraction</button>
            <a href="{{ route('admin.attractions.index') }}" class="text-gray-600 hover:underline ml-4">Cancel</a>
        </div>
    </form>
</div>
@endsection
