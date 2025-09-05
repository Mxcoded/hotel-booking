@extends('layouts.admin')

@section('content')
<h1 class="text-3xl font-bold mb-6">Edit Attraction</h1>

<div class="bg-white p-8 rounded-lg shadow-md">
    <form action="{{ route('admin.attractions.update', $attraction) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.attractions._form', ['attraction' => $attraction])
        <div class="mt-8">
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Update Attraction</button>
            <a href="{{ route('admin.attractions.index') }}" class="text-gray-600 hover:underline ml-4">Cancel</a>
        </div>
    </form>
</div>
@endsection
