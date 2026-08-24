<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->handleFileUpload();
        Cache::forget('settings');
    }

    protected function handleFileUpload(): void
    {
        $record = $this->record;

        if (! in_array($record->type, ['image', 'video', 'file'])) {
            return;
        }

        $fileState = $this->form
            ->getComponent(fn ($component) => $component->getName() === 'file_upload')
            ?->getState();

        if (! filled($fileState)) {
            return;
        }

        $path = is_array($fileState) ? reset($fileState) : $fileState;

        if (is_string($path) && $path !== $record->value) {
            if ($record->value && Storage::disk('public')->exists($record->value)) {
                Storage::disk('public')->delete($record->value);
            }

            $record->update(['value' => $path]);
        }
    }
}
