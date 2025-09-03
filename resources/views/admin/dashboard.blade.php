@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Dashboard Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Welcome Back, {{ Auth::user()->name }}!</h1>
        <p class="text-gray-600">Here's a snapshot of your website's activity. Today is {{ now()->format('l, F j, Y') }}.</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Rooms Card -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex items-center justify-between">
            <div>
                <h2 class="text-gray-500 text-sm font-medium">Total Rooms</h2>
                <p class="text-3xl font-bold text-gray-800">{{ $roomCount }}</p>
            </div>
            <div class="bg-blue-100 text-blue-500 rounded-full p-3">
                <i class="fas fa-bed fa-lg"></i>
            </div>
        </div>
        <!-- Gallery Images Card -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex items-center justify-between">
            <div>
                <h2 class="text-gray-500 text-sm font-medium">Gallery Images</h2>
                <p class="text-3xl font-bold text-gray-800">{{ $galleryCount }}</p>
            </div>
            <div class="bg-purple-100 text-purple-500 rounded-full p-3">
                <i class="fas fa-images fa-lg"></i>
            </div>
        </div>
        <!-- Unread Messages Card -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex items-center justify-between">
            <div>
                <h2 class="text-gray-500 text-sm font-medium">Unread Messages</h2>
                <p class="text-3xl font-bold text-red-500">{{ $unreadMessages }}</p>
            </div>
            <div class="bg-red-100 text-red-500 rounded-full p-3">
                <i class="fas fa-envelope fa-lg"></i>
            </div>
        </div>
        <!-- WhatsApp Leads Card -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex items-center justify-between">
            <div>
                <h2 class="text-gray-500 text-sm font-medium">WhatsApp Leads</h2>
                <p class="text-3xl font-bold text-green-500">{{ $whatsappLeadsCount }}</p>
            </div>
            <div class="bg-green-100 text-green-500 rounded-full p-3">
                <i class="fab fa-whatsapp fa-lg"></i>
            </div>
        </div>
    </div>

    <!-- Visitor Chart -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Unique Visitors (Last 7 Days)</h2>
                <p class="text-sm text-gray-500">A look at daily unique visitors to your site.</p>
            </div>
            <div class="text-left sm:text-right mt-4 sm:mt-0">
                <p class="text-gray-600 text-sm font-medium">Today's Visitors</p>
                <p class="text-2xl font-bold text-gray-800">{{ $todayVisitors }}</p>
            </div>
        </div>
        <div class="h-80">
             <canvas id="visitorChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('visitorChart').getContext('2d');
        const visitorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($visitorLabels),
                datasets: [{
                    label: 'Unique Visitors',
                    data: @json($visitorData),
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
@endsection

