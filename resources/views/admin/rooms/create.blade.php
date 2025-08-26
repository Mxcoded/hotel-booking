@extends('layouts.admin')

@section('title', 'Add New Room')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-6">Create a New Room</h2>

        <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- Include the reusable form partial --}}
            @include('admin.rooms._form')

            <div class="mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Save Room</button>
                <a href="{{ route('admin.rooms.index') }}" class="text-gray-600 hover:text-gray-800 ml-4">Cancel</a>
            </div>
        </form>
    </div>
@endsection
