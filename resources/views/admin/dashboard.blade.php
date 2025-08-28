@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Rooms Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white p-3 rounded-full">
                    <i class="fas fa-bed text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Total Rooms</h3>
                    <p class="text-3xl font-bold">{{ $roomCount }}</p>
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
                    <p class="text-3xl font-bold">{{ $galleryCount }}</p>
                </div>
            </div>
        </div>

        <!-- Unread Messages Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white p-3 rounded-full">
                    <i class="fas fa-envelope text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Unread Messages</h3>
                    <p class="text-3xl font-bold">{{ $unreadMessagesCount }}</p>
                </div>
            </div>
        </div>
        
        <!-- Today's Visitors Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="bg-purple-500 text-white p-3 rounded-full">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Today's Visitors</h3>
                    <p class="text-3xl font-bold">{{ $todaysVisitors }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitor Chart -->
    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-4">Visitor Insights (Last 7 Days)</h3>
        <div>
            <canvas id="visitorChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('visitorChart').getContext('2d');
            const visitorChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Unique Visitors',
                        data: @json($chartData),
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
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
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
@endsection
