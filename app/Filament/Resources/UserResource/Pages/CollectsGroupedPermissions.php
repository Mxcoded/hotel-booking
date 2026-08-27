<?php

namespace App\Filament\Resources\UserResource\Pages;

use Spatie\Permission\Models\Permission;

trait CollectsGroupedPermissions
{
    protected array $selectedPermissions = [];

    protected function collectPermissionFields(array $data): array
    {
        $permissionIds = [];

        foreach ($data as $key => $value) {
            if (str_starts_with((string) $key, 'perm_') && is_array($value)) {
                $permissionIds = array_merge($permissionIds, array_values($value));
            }
        }

        return array_map('strval', array_values($permissionIds));
    }

    protected function resolvePermissionNames(array $ids): array
    {
        return Permission::whereIn('id', $ids)->pluck('name')->all();
    }
}
