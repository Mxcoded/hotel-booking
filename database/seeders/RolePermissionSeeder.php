<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Generate standard CRUD permissions for a model.
     * Laravel policies use: viewAny, view, create, update, delete, deleteAny, forceDelete, restore, reorder
     * Permission names: view_any_{model}, view_{model}, create_{model}, update_{model}, delete_{model},
     * delete_any_{model}, force_delete_{model}, restore_{model}, reorder_{model}
     */
    protected function modelPermissions(string $model): array
    {
        $actions = [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'delete_any',
            'force_delete',
            'restore',
            'reorder',
        ];

        $perms = array_map(fn ($action) => "{$action}_{$model}", $actions);

        // Add custom actions for specific models
        if ($model === 'feedback') {
            $perms[] = 'approve_feedback';
        }
        if ($model === 'reservation') {
            $perms[] = 'confirm_reservation';
            $perms[] = 'cancel_reservation';
        }
        if ($model === 'availability_calendar') {
            $perms[] = 'manage_availability_calendar';
        }
        if ($model === 'food_menu') {
            $perms[] = 'manage_food_menu';
        }
        if ($model === 'whatsapp_lead') {
            $perms[] = 'delete_whatsapp_lead';
        }
        if ($model === 'user') {
            $perms[] = 'assign_roles_user';
            $perms[] = 'assign_permissions_user';
        }
        if ($model === 'room_type') {
            $perms[] = 'import_room_type';
        }

        return $perms;
    }

    public function run(): void
    {
        // Define all models used in the admin panel with their snake_case names
        $models = [
            'contact',
            'feedback',
            'gallery',
            'reservation',
            'room_type',
            'room_unit',
            'room_media',
            'attraction',
            'setting',
            'whatsapp_lead',
            'user',
            'role',
            'permission',
        ];

        // Generate standard CRUD permissions for all models
        $allPermissions = [];
        foreach (['contact', 'feedback', 'gallery', 'reservation', 'room_type', 'room_unit', 'room_media', 'attraction', 'setting', 'whatsapp_lead', 'user', 'role', 'permission', 'availability_calendar', 'food_menu', 'audit_log'] as $model) {
            $allPermissions = array_merge($allPermissions, $this->modelPermissions($model));
        }

        // Add special/custom permissions (for non-model resources like pages)
        $specialPermissions = [
            'dashboard.view',
            'availability_calendar.view_any',
            'availability_calendar.view',
            'availability_calendar.manage',
            'reservations.confirm',
            'reservations.cancel',
            'feedback.approve',
            'whatsapp_leads.view',
            'whatsapp_leads.delete',
            'food_menu.view',
            'food_menu.manage',
            'audit_log.view',
            'users.assign_roles',
            'users.assign_permissions',
            'room_types.import',
            'food_menu.manage',
        ];

        $allPermissions = array_merge($allPermissions, $specialPermissions);

        // Create all permissions
        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Define roles with their permissions
        $roles = [
            'super_admin' => [
                '*' => true,
            ],
            'admin' => [
                'dashboard.view',
                'contact.*',
                'feedback.*',
                'gallery.*',
                'reservation.*',
                'room_type.*',
                'room_unit.*',
                'room_media.*',
                'attraction.*',
                'setting.*',
                'whatsapp_lead.*',
                'availability_calendar.*',
                'reservations.confirm',
                'reservations.cancel',
                'feedback.approve',
                'whatsapp_leads.*',
                'food_menu.*',
                'audit_log.view',
            ],
            'manager' => [
                'dashboard.view',
                'view_any_contact',
                'view_contact',
                'view_any_feedback',
                'view_feedback',
                'approve_feedback',
                'view_any_gallery',
                'view_gallery',
                'view_any_reservation',
                'view_reservation',
                'create_reservation',
                'update_reservation',
                'confirm_reservation',
                'cancel_reservation',
                'view_any_room_type',
                'view_room_type',
                'view_any_room_unit',
                'view_room_unit',
                'view_any_room_media',
                'view_any_gallery',
                'view_any_attraction',
                'view_attraction',
                'view_any_setting',
                'view_setting',
                'view_any_whatsapp_lead',
                'view_any_availability_calendar',
                'view_availability_calendar',
                'manage_availability_calendar',
                'view_any_reservation',
                'view_reservation',
                'create_reservation',
                'update_reservation',
                'confirm_reservation',
                'cancel_reservation',
                'view_any_feedback',
                'view_feedback',
                'approve_feedback',
                'view_any_whatsapp_lead',
                'view_food_menu',
                'manage_food_menu',
                'view_audit_log',
            ],
            'receptionist' => [
                'dashboard.view',
                'view_any_room_type',
                'view_room_type',
                'view_any_room_unit',
                'view_room_unit',
                'view_any_availability_calendar',
                'view_availability_calendar',
                'manage_availability_calendar',
                'view_any_reservation',
                'view_reservation',
                'create_reservation',
                'update_reservation',
                'confirm_reservation',
                'cancel_reservation',
                'view_any_contact',
                'view_contact',
            ],
            'housekeeping' => [
                'dashboard.view',
                'view_any_room_unit',
                'view_room_unit',
                'view_any_availability_calendar',
                'view_availability_calendar',
            ],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            if (isset($perms['*']) && $perms['*']) {
                $role->syncPermissions(Permission::all());
            } else {
                $permissionNames = [];
                foreach ($perms as $perm) {
                    if (str_ends_with($perm, '.*')) {
                        $prefix = rtrim($perm, '.*');
                        $permissionNames = array_merge(
                            $permissionNames,
                            Permission::where('name', 'like', $prefix . '.%')->pluck('name')->toArray()
                        );
                    } else {
                        $permissionNames[] = $perm;
                    }
                }
                $role->syncPermissions($permissionNames);
            }
        }

        // Ensure default admin user exists and has super_admin role
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@brickspoint.ng'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['super_admin']);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}