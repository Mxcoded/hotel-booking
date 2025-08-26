@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Rooms Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white p-3 rounded-full">
                    <i class="fas fa-bed text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Total Rooms</h3>
                    <p class="text-3xl font-bold">12</p> {{-- Replace with dynamic data later --}}
                </div>
            </div>
        </div>

        <!-- Gallery Images Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="bg-green-500 text-white p-3 rounded-full">
                    <i class="fas fa-images text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Gallery Images</h3>
                    <p class="text-3xl font-bold">34</p> {{-- Replace with dynamic data later --}}
                </div>
            </div>
        </div>

        <!-- Placeholder Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white p-3 rounded-full">
                    <i class="fas fa-envelope text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Contact Messages</h3>
                    <p class="text-3xl font-bold">5</p> {{-- Replace with dynamic data later --}}
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-4">Welcome to your Dashboard!</h3>
        <p class="text-gray-600">From here, you will be able to manage all aspects of your hotel website. Use the navigation on the left to manage rooms, update the gallery, and change site settings.</p>
    </div>
@endsection
