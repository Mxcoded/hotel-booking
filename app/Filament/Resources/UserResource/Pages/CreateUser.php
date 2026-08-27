<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use CollectsGroupedPermissions;

    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'User created successfully';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->selectedPermissions = $this->collectPermissionFields($data);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncPermissions($this->resolvePermissionNames($this->selectedPermissions));
    }
}