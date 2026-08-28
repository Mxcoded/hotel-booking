<x-filament::page>
    <div class="space-y-6">
        {{-- Month selector --}}
        <div class="max-w-xs">
            {{ $this->form }}
        </div>

        @php($report = $this->getReport())

        {{-- Key metrics --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Occupancy</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $report['occupancy'] }}%</p>
                <p class="text-xs text-gray-400">{{ $report['booked_nights'] }} / {{ $report['total_nights'] }} room-nights
                    ({{ $report['active_units'] }} units)</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Bookings</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $report['bookings'] }}</p>
                <p class="text-xs text-gray-400">for {{ $report['month_label'] }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue (booked)</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">₦{{ number_format($report['revenue_potential']) }}</p>
                <p class="text-xs text-gray-400">potential stay value</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Collected</p>
                <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">₦{{ number_format($report['collected']) }}</p>
                <p class="text-xs text-gray-400">payments received</p>
            </div>
        </div>

        {{-- Revenue by room type --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Revenue by room type</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs text-gray-400 dark:border-gray-700">
                            <th class="py-2 pr-4 font-medium">Room type</th>
                            <th class="py-2 pr-4 font-medium">Bookings</th>
                            <th class="py-2 font-medium">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['by_room_type'] as $name => $data)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 pr-4 font-medium text-gray-800 dark:text-gray-200">{{ $name }}</td>
                                <td class="py-2 pr-4 text-gray-600 dark:text-gray-400">{{ $data['count'] }}</td>
                                <td class="py-2 text-gray-900 dark:text-white">₦{{ number_format($data['revenue']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-400">No bookings in this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament::page>
