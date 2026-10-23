<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected function afterCreate(): void
    {
        $this->handleFileUpload();
    }

    protected function handleFileUpload(): void
    {
        $record = $this->record;

        if (! in_array($record->type, ['image', 'video', 'file'])) {
            return;
        }

        $fileState = $this->form->getState()['file_upload'] ?? null;

        if (! filled($fileState)) {
            return;
        }

        $path = is_array($fileState) ? reset($fileState) : $fileState;

        if (is_string($path)) {
            $record->update(['value' => $path]);
        }
    }
}
