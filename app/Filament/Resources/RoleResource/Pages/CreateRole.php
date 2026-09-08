<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\UserResource\Pages\CollectsGroupedPermissions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    use CollectsGroupedPermissions;

    protected static string $resource = RoleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->selectedPermissions = $this->collectPermissionFields($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncPermissions($this->resolvePermissionNames($this->selectedPermissions));
    }
}
