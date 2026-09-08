<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\UserResource\Pages\CollectsGroupedPermissions;
use App\Support\PermissionGroups;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditRole extends EditRecord
{
    use CollectsGroupedPermissions;

    protected static string $resource = RoleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getUpdatedNotificationTitle(): ?string
    {
        return 'Role updated successfully';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->selectedPermissions = $this->collectPermissionFields($data);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->syncPermissions($this->resolvePermissionNames($this->selectedPermissions));
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach (PermissionGroups::groupedPermissionOptions() as $label => $options) {
            $field = 'perm_' . Str::slug($label, '_');
            $data[$field] = $this->record->permissions()->whereIn('id', array_keys($options))->pluck('id')->map(fn ($id) => (string) $id)->all();
        }

        return $data;
    }
}
