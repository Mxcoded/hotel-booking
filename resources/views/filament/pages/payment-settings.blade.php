<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="submit">
            {{ $this->form }}

            <div class="flex items-center gap-3 mt-6">
                <x-filament::button type="submit" wire:loading.attr="disabled" wire:target="submit">
                    <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                    Save Payment Settings
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
