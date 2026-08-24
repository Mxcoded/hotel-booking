<x-filament::page>
    <div class="space-y-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Automatic audit trail of booking requests and room type changes. Entries are immutable.
        </p>

        {{ $this->table }}
    </div>
</x-filament::page>
