@extends('layouts.app')

@section('title', 'Leave Feedback - Brickspoint')

@section('content')
    <div class="container mx-auto my-12 max-w-md">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            
            {{-- This block handles the success message for non-JS users --}}
            @if (session('success'))
                <div class="text-center">
                    <div class="text-green-500 mb-4">
                        <i class="fas fa-check-circle fa-4x"></i>
                    </div>
                    <h3 class="text-2xl font-bold">Thank You!</h3>
                    <p class="text-gray-600 mt-2">{{ session('success') }}</p>
                </div>
            @else
                {{-- If no success, show the form. 
                     The JS from app.blade.php will automatically 
                     make this form submission AJAX-powered. --}}
                @include('partials._feedback_form_content')
            @endif

        </div>
    </div>
@endsection