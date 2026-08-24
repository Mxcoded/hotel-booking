<x-filament::page>
    <div class="space-y-6">
        {{-- Import Form --}}
        <form wire:submit="submit">
            {{ $this->form }}

            <div class="flex items-center gap-3 mt-6">
                <x-filament::button type="submit" wire:loading.attr="disabled" wire:target="submit">
                    <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2" />
                    Import Room Types
                </x-filament::button>

                <a href="{{ $this->getSampleDownloadUrl() }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-1 inline" />
                    Download Sample Excel
                </a>
            </div>
        </form>

        {{-- Column Reference --}}
        <x-filament::section>
            <x-slot name="heading">
                Column Reference
            </x-slot>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">name *</code>
                    <span class="text-gray-600 dark:text-gray-400">Room type name</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">room_type *</code>
                    <span class="text-gray-600 dark:text-gray-400">standard|deluxe|suite|executive|presidential</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">bed_type</code>
                    <span class="text-gray-600 dark:text-gray-400">king|queen|twin|double|sofa_bed</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">price *</code>
                    <span class="text-gray-600 dark:text-gray-400">Base price per night (₦)</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">weekend_price</code>
                    <span class="text-gray-600 dark:text-gray-400">Fri/Sat price (₦)</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">holiday_price</code>
                    <span class="text-gray-600 dark:text-gray-400">Holiday price (₦)</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">room_size</code>
                    <span class="text-gray-600 dark:text-gray-400">Size in sqm</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">description</code>
                    <span class="text-gray-600 dark:text-gray-400">Room description</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">base_guests</code>
                    <span class="text-gray-600 dark:text-gray-400">Included guests (default: 2)</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">max_guests</code>
                    <span class="text-gray-600 dark:text-gray-400">Max occupancy</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">extra_guest_fee</code>
                    <span class="text-gray-600 dark:text-gray-400">Fee per extra guest (₦)</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">discount_percent</code>
                    <span class="text-gray-600 dark:text-gray-400">Discount % (0-100)</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">min_stay</code>
                    <span class="text-gray-600 dark:text-gray-400">Min nights (default: 1)</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">max_stay</code>
                    <span class="text-gray-600 dark:text-gray-400">Max nights</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">check_in_time</code>
                    <span class="text-gray-600 dark:text-gray-400">HH:MM (default: 14:00)</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">check_out_time</code>
                    <span class="text-gray-600 dark:text-gray-400">HH:MM (default: 11:00)</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">is_active</code>
                    <span class="text-gray-600 dark:text-gray-400">true|false (default: true)</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">is_featured</code>
                    <span class="text-gray-600 dark:text-gray-400">true|false (default: false)</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">features</code>
                    <span class="text-gray-600 dark:text-gray-400">Name|icon,Name|icon</span>
                </div>
                <div class="flex items-start gap-2">
                    <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">sort_order</code>
                    <span class="text-gray-600 dark:text-gray-400">Display order (default: 0)</span>
                </div>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">* Required columns. Existing rooms (matched by slug) will be updated. Features format: <code class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded font-mono">Free WiFi|fa-wifi,Mini Bar|fa-glass-whiskey</code></p>
        </x-filament::section>

        {{-- Import Results --}}
        @if($importResults)
            <x-filament::section>
                <x-slot name="heading">
                    Last Import Results
                </x-slot>

                <div class="space-y-2 text-sm">
                    @if($importResults['created'] > 0)
                        <div class="flex items-center gap-2 text-success-700 dark:text-success-500">
                            <x-heroicon-o-check-circle class="w-5 h-5" />
                            <span>{{ $importResults['created'] }} room type(s) created</span>
                        </div>
                    @endif
                    @if($importResults['updated'] > 0)
                        <div class="flex items-center gap-2 text-primary-700 dark:text-primary-500">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                            <span>{{ $importResults['updated'] }} room type(s) updated</span>
                        </div>
                    @endif
                    @if($importResults['skipped'] > 0)
                        <div class="flex items-center gap-2 text-warning-700 dark:text-warning-500">
                            <x-heroicon-o-minus-circle class="w-5 h-5" />
                            <span>{{ $importResults['skipped'] }} row(s) skipped</span>
                        </div>
                    @endif
                    @if(!empty($importResults['errors']))
                        <div class="mt-3 space-y-1">
                            @foreach($importResults['errors'] as $error)
                                <div class="text-xs text-danger-600 dark:text-danger-400 bg-danger-50 dark:bg-danger-950 rounded-lg px-3 py-2">
                                    {{ $error }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament::page>
