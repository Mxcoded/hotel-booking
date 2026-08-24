<x-filament-widgets::widget>
    <x-filament::widget class="fi-wc-booking-funnel">
        <x-filament::section>
            <x-slot name="heading">
                Booking Funnel
            </x-slot>
            <x-slot name="description">
                {{ $this->periodLabel }} · overall conversion {{ $this->overallConversion }}%
            </x-slot>

            <div class="space-y-4">
                @foreach ($this->steps as $i => $step)
                    <div>
                        <div class="flex items-baseline justify-between gap-3 text-sm">
                            <span class="font-semibold text-gray-700 dark:text-gray-200">
                                {{ $i + 1 }}. {{ $step['label'] }}
                            </span>
                            <span class="flex items-center gap-2">
                                @if ($step['conversion'] !== null)
                                    <span class="text-xs font-medium {{ $step['conversion'] >= 25 ? 'text-success-600 dark:text-success-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $step['conversion'] }}% of previous
                                    </span>
                                @endif
                                <strong class="text-lg tabular-nums">{{ number_format($step['count']) }}</strong>
                            </span>
                        </div>

                        <div class="mt-1.5 h-3 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full rounded-full transition-all {{ $step['color'] }}"
                                 style="width: {{ max(2, $step['width']) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                Visitors → WhatsApp clicks → booking requests submitted on-site → stays confirmed by staff.
            </p>
        </x-filament::section>
    </x-filament::widget>
</x-filament-widgets::widget>
