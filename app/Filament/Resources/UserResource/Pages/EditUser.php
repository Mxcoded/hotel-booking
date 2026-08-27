<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Support\PermissionGroups;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditUser extends EditRecord
{
    use CollectsGroupedPermissions;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()->can('users.delete') && $this->record->id !== auth()->id()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getUpdatedNotificationTitle(): ?string
    {
        return 'User updated successfully';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->selectedPermissions = $this->collectPermissionFields($data);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->syncPermissions($this->resolvePermissionNames($this->selectedPermissions));
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach (PermissionGroups::groupedPermissionOptions() as $label => $options) {
            $field = 'perm_' . Str::slug($label);
            $data[$field] = $this->record->permissions()->whereIn('id', array_keys($options))->pluck('id')->map(fn ($id) => (string) $id)->all();
        }

        return $data;
    }
}