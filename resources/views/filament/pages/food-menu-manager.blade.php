<x-filament::page>
    <div class="space-y-6">
        {{-- Upload Form --}}
        <form wire:submit="submit">
            {{ $this->form }}

            <div class="flex items-center gap-3 mt-6">
                <x-filament::button type="submit" wire:loading.attr="disabled" wire:target="submit">
                    <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2" />
                    Upload PDF
                </x-filament::button>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                Uploading a new PDF will automatically replace the existing one. Max file size: 10MB.
            </p>
        </form>

        {{-- Current Menu --}}
        <x-filament::section>
            <x-slot name="heading">
                Current Food Menu
            </x-slot>

            @if($this->hasMenu())
                <div class="flex flex-col lg:flex-row items-start gap-6">
                    <div class="flex-1 w-full min-w-0">
                        <iframe
                            src="{{ $this->getMenuUrl() }}"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm bg-white"
                            style="height: 600px;"
                            title="Food Menu Preview"
                            loading="lazy">
                        </iframe>
                    </div>

                    <div class="flex flex-col gap-3 shrink-0 w-full lg:w-auto">
                        <a href="{{ $this->getMenuUrl() }}" target="_blank" class="w-full">
                            <x-filament::button tag="a" href="{{ $this->getMenuUrl() }}" target="_blank" color="gray" class="w-full">
                                <x-heroicon-o-eye class="w-5 h-5 mr-2" />
                                View Full PDF
                            </x-filament::button>
                        </a>

                        <x-filament::button
                            tag="a"
                            href="{{ $this->getMenuUrl() }}"
                            download
                            color="warning"
                            class="w-full">
                            <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                            Download
                        </x-filament::button>

                        <x-filament::button
                            wire:click="removeMenu"
                            onclick="return confirm('Are you sure you want to remove the current food menu?')"
                            color="danger"
                            class="w-full">
                            <x-heroicon-o-trash class="w-5 h-5 mr-2" />
                            Remove PDF
                        </x-filament::button>
                    </div>
                </div>
            @else
                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-document-text class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
                    <p class="text-lg">No food menu has been uploaded yet.</p>
                    <p class="text-sm mt-1">Use the form above to upload a PDF.</p>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament::page>
