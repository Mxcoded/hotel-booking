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

        $fileState = $this->form->getComponent('file_upload')?->getRawState();

        $files = collect(is_array($fileState) ? $fileState : [$fileState])
            ->filter(fn ($file) => is_string($file) && filled($file))
            ->values();

        $path = $files->last();

        if (! is_string($path)) {
            return;
        }

        if ($path !== $record->value) {
            if ($record->value && is_string($record->value) && Storage::disk('public')->exists($record->value)) {
                Storage::disk('public')->delete($record->value);
            }

            $record->update(['value' => $path]);
        }
    }
}
