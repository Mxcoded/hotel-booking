<?php

namespace App\Support;

use Spatie\Permission\Models\Permission;

class PermissionGroups
{
    /**
     * Map each permission name to its feature/module group.
     * Keys = group label, values = regex suffix used to match permission name.
     */
    public static function groups(): array
    {
        return [
            'Dashboard' => ['dashboard.view'],
            'Contact Messages' => ['contact'],
            'Guest Feedback' => ['feedback', 'approve_feedback'],
            'Photo Gallery' => ['gallery'],
            'Reservations' => ['reservation', 'reservations', 'confirm_reservation', 'cancel_reservation'],
            'Room Types' => ['room_type', 'room_types', 'import_room_type'],
            'Room Units' => ['room_unit'],
            'Room Media' => ['room_media'],
            'Local Guide (Attractions)' => ['attraction'],
            'Site Settings' => ['setting'],
            'WhatsApp Leads' => ['whatsapp_lead', 'whatsapp_leads.view', 'whatsapp_leads.delete'],
            'Availability Calendar' => ['availability_calendar'],
            'Food Menu' => ['food_menu'],
            'Audit Log' => ['audit_log'],
            'User Management' => ['user', 'role', 'permission', 'users.assign_roles', 'users.assign_permissions', 'assign_roles_user', 'assign_permissions_user'],
        ];
    }

    /**
     * Group permissions by feature, returning [groupLabel => [permId => readableLabel]].
     */
    public static function groupedPermissionOptions(): array
    {
        $permissions = Permission::orderBy('name')->get();

        $result = [];
        foreach (self::groups() as $label => $suffixes) {
            $result[$label] = [];
        }

        foreach ($permissions as $permission) {
            foreach (self::groups() as $label => $suffixes) {
                foreach ($suffixes as $suffix) {
                    if (self::matches($permission->name, $suffix)) {
                        $result[$label][(string) $permission->id] = ucwords(str_replace(['_', '.'], ' ', $permission->name));
                        break 2;
                    }
                }
            }
        }

        // Drop groups that matched nothing
        return array_filter($result, fn ($opts) => count($opts) > 0);
    }

    protected static function matches(string $permissionName, string $suffix): bool
    {
        if (str_contains($suffix, '.')) {
            return $permissionName === $suffix;
        }

        return str_starts_with($permissionName, 'view_any_' . $suffix)
            || str_starts_with($permissionName, $suffix . '_')
            || str_starts_with($permissionName, $suffix . '.')
            || str_ends_with($permissionName, '_' . $suffix)
            || $permissionName === $suffix;
    }
}
