<x-filament::page>
    <div class="space-y-4" wire:poll.30s="refreshCalendar">
        {{-- Header: navigation + live status --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-1.5">
                <x-filament::icon-button icon="heroicon-o-chevron-left" label="Previous month"
                    wire:click="previousMonth" wire:loading.attr="disabled" wire:target="previousMonth" />

                <select wire:model.live="month"
                        aria-label="Jump to month"
                        class="rounded-lg border-gray-300 bg-white py-1.5 pl-2 pr-7 text-sm font-semibold text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200">
                    @foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $name)
                        <option value="{{ $i + 1 }}" {{ $month === $i + 1 ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="year"
                        aria-label="Jump to year"
                        class="rounded-lg border-gray-300 bg-white py-1.5 pl-2 pr-7 text-sm font-semibold text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200">
                    @foreach ($this->yearOptions as $option)
                        <option value="{{ $option }}" {{ $year == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>

                <span class="text-sm font-medium text-gray-400" aria-hidden="true">
                    {{ $this->monthStart->format('F Y') }}
                </span>

                <x-filament::icon-button icon="heroicon-o-chevron-right" label="Next month"
                    wire:click="nextMonth" wire:loading.attr="disabled" wire:target="nextMonth" />
                <x-filament::button size="sm" color="gray" icon="heroicon-o-calendar"
                    wire:click="goToToday" wire:target="goToToday">
                    Today
                </x-filament::button>
            </div>

            <span class="inline-flex items-center gap-2 rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-500 ring-1 ring-inset ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                </span>
                Live — updates automatically
            </span>
        </div>

        {{-- Stats strip --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4" wire:loading.delay.class="opacity-60 transition-opacity" wire:target="handleCellClick">
            <div class="rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Active units</p>
                <p class="mt-0.5 text-xl font-bold text-gray-900 tabular-nums dark:text-white">{{ $this->stats['total_units'] }}</p>
            </div>
            <div class="rounded-xl border border-red-100 bg-red-50/60 p-3 dark:border-red-900/40 dark:bg-red-950/30">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-red-400">Booked nights</p>
                <p class="mt-0.5 text-xl font-bold text-red-600 tabular-nums dark:text-red-400">{{ $this->stats['booked_nights'] }}</p>
            </div>
            <div class="rounded-xl border border-orange-100 bg-orange-50/60 p-3 dark:border-orange-900/40 dark:bg-orange-950/30">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-orange-500">Blocked / maint.</p>
                <p class="mt-0.5 text-xl font-bold text-orange-600 tabular-nums dark:text-orange-400">
                    {{ $this->stats['blocked_nights'] }} / {{ $this->stats['maintenance_nights'] }}
                </p>
            </div>
            <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-3 dark:border-emerald-900/40 dark:bg-emerald-950/30">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-500">Occupancy</p>
                <div class="mt-1 flex items-center gap-2">
                    <p class="text-xl font-bold text-emerald-700 tabular-nums dark:text-emerald-300">{{ $this->stats['occupancy_percent'] }}%</p>
                    <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-emerald-100 dark:bg-emerald-900/60">
                        <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ min(100, $this->stats['occupancy_percent']) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-medium text-gray-600 dark:text-gray-300">
            <span class="inline-flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded border border-green-300 bg-green-100"></span> Available</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-red-500"></span> Booked</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-orange-400"></span> Blocked — click to release</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-slate-400"></span> Maintenance — use “Mark Maintenance”</span>
            <span class="inline-flex items-center gap-1.5 text-indigo-500">
                <x-heroicon-m-command-line class="h-3.5 w-3.5" /> Click a day, then Shift-click another to block/release a whole stretch
            </span>
        </div>

        {{-- Grid --}}
        <div class="relative overflow-x-auto rounded-lg border border-gray-200 shadow-sm dark:border-gray-700">
            {{-- Full-area loading veil --}}
            <div wire:loading wire:target="previousMonth,nextMonth,goToToday"
                 class="absolute inset-0 z-20 flex items-center justify-center bg-white/70 backdrop-blur-sm dark:bg-gray-900/70">
                <x-heroicon-o-arrow-path class="h-8 w-8 animate-spin text-indigo-500" />
            </div>

            <table class="cal-grid w-full min-w-max border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <th scope="col" class="sticky left-0 z-10 min-w-[11rem] border-b border-r border-gray-200 bg-gray-50 px-3 py-2 text-left font-bold dark:border-gray-700 dark:bg-gray-800">
                            Unit
                        </th>
                        @foreach ($this->daysInMonth as $day)
                            <th scope="col"
                                title="{{ $day->format('l, d M Y') }}"
                                class="border-b border-l border-gray-200 px-0 py-2 {{ $day->isWeekend() ? 'bg-gray-100 dark:bg-gray-700' : '' }} {{ $day->isToday() ? 'bg-indigo-50 dark:bg-indigo-900/40' : '' }}">
                                <div class="{{ $day->isToday() ? 'font-extrabold text-indigo-600 dark:text-indigo-300' : '' }}">{{ $day->format('d') }}</div>
                                <div class="text-[10px] font-normal text-gray-500">{{ $day->format('D') }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                @forelse ($this->unitGroups as $typeName => $units)
                    <tbody class="cal-body">
                        <tr class="bg-indigo-50/60 dark:bg-indigo-900/20">
                            <td colspan="{{ $this->daysInMonth->count() + 1 }}" class="sticky left-0 z-10 bg-indigo-50/95 px-3 py-1.5 font-bold text-indigo-800 backdrop-blur-sm dark:bg-indigo-950/80 dark:text-indigo-300">
                                {{ $typeName }} ({{ $units->count() }})
                            </td>
                        </tr>
                        @foreach ($units as $unit)
                            <tr class="group hover:bg-gray-50/60 dark:hover:bg-gray-800/40">
                                <th scope="row" class="sticky left-0 z-10 whitespace-nowrap bg-white px-3 py-1.5 text-left font-semibold group-hover:bg-gray-50 dark:bg-gray-900 dark:group-hover:bg-gray-800">
                                    {{ $unit->unit_number }}
                                    @if($unit->floor)
                                        <span class="ml-1 text-[10px] font-normal text-gray-400">F{{ $unit->floor }}</span>
                                    @endif
                                </th>
                                @foreach ($this->daysInMonth as $day)
                                    @php
                                        $status = $this->statusFor($unit->id, $day);
                                        $past = $day->isPast() && ! $day->isToday();
                                        $todayCol = $day->isToday() ? 'bg-indigo-50/40 dark:bg-indigo-900/10' : '';
                                        $guest = $status === 'booked' ? ($this->bookingLabels[$unit->id][$day->toDateString()] ?? null) : null;
                                    @endphp
                                    <td class="border-l border-gray-100 p-0 text-center align-middle {{ $todayCol }} dark:border-gray-800"
                                        title="{{ $this->cellTitle($unit->id, $day) }}">
                                        @if ($past)
                                            <div class="{{ $this->cellClass($unit->id, $day) }} mx-auto my-0.5 flex h-7 w-full max-w-7 items-center justify-center rounded"
                                                 aria-hidden="true">
                                                @if($status === 'booked')
                                                    <x-heroicon-o-lock-closed class="h-3 w-3 opacity-60" />
                                                @endif
                                            </div>
                                        @elseif ($status === 'booked')
                                            <div class="{{ $this->cellClass($unit->id, $day) }} mx-auto my-0.5 flex h-7 w-full max-w-7 items-center justify-center rounded">
                                                @if ($guest)
                                                    <span class="truncate px-0.5 text-[9px] font-bold uppercase tracking-wide">{{ \Illuminate\Support\Str::of($guest)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 1))->implode('') }}</span>
                                                @else
                                                    <x-heroicon-o-lock-closed class="h-3 w-3" />
                                                @endif
                                            </div>
                                        @else
                                            <button type="button"
                                                    wire:click="handleCellClick({{ $unit->id }}, '{{ $day->toDateString() }}', $event.shiftKey)"
                                                    wire:loading.attr="disabled" wire:target="handleCellClick"
                                                    aria-label="{{ $this->cellLabel($unit->id, $day) }}"
                                                    title="{{ $this->cellTitle($unit->id, $day) }}"
                                                    class="{{ $this->cellClass($unit->id, $day) }} mx-auto my-0.5 flex h-7 w-full max-w-7 items-center justify-center rounded transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-indigo-500 active:scale-90">
                                                @if($status === 'maintenance')
                                                    <x-heroicon-o-wrench-screwdriver class="h-3 w-3" />
                                                @endif
                                            </button>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="32" class="px-4 py-12 text-center">
                                <x-heroicon-o-calendar-days class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" />
                                <p class="mt-3 font-semibold text-gray-500 dark:text-gray-400">No active room units found</p>
                                <p class="mt-1 text-sm text-gray-400">Add units under Room Types to start managing availability.</p>
                            </td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>

        <p class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
            <span>Click any future date to block a unit · Shift-click a second date to block the whole span.</span>
            <span>Hover a red cell to see who holds the room.</span>
            <span>To take a unit out of order (repairs, cleaning, etc.), use <strong>Mark Maintenance</strong> above — “Clear Maintenance” puts it back on sale.</span>
            <span class="text-gray-400">Confirmed bookings appear here automatically · refreshes every 30s</span>
        </p>
    </div>
</x-filament::page>
