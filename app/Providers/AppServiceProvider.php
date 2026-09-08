<?php

namespace App\Providers;

use Filament\Resources\Resource;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureGate();
        $this->configureFilamentAuthorization();
    }

    /**
     * Allow only super_admin through all Gate checks, then enforce Spatie
     * permissions for everyone else.
     *
     * Super admin bypasses everything. For all other authenticated users,
     * Filament's model abilities (viewAny, view, create, update, delete, ...)
     * are translated into the matching Spatie permission (e.g. view_any_user)
     * and enforced. Bare permission-style abilities (e.g. users.delete,
     * manage_availability_calendar) are also enforced when they match a
     * registered permission.
     */
    protected function configureGate(): void
    {
        Gate::before(function (User $user, string $ability, array $arguments) {
            if (! $user instanceof \App\Models\User) {
                return null;
            }

            if ($user->hasRole('super_admin')) {
                return true;
            }

            $model = $arguments[0] ?? null;

            if (is_object($model) || is_string($model)) {
                $permission = $this->abilityToPermission($ability, $model);

                if ($permission !== null) {
                    return $this->userHasPermission($user, $permission);
                }
            }

            return $this->userHasPermission($user, $ability);
        });
    }

    protected function abilityToPermission(string $ability, mixed $model): ?string
    {
        $abilityMap = [
            'viewAny' => 'view_any',
            'view' => 'view',
            'create' => 'create',
            'update' => 'update',
            'delete' => 'delete',
            'deleteAny' => 'delete_any',
            'forceDelete' => 'force_delete',
            'restore' => 'restore',
            'reorder' => 'reorder',
        ];

        if (! array_key_exists($ability, $abilityMap)) {
            return null;
        }

        $class = is_object($model) ? get_class($model) : $model;

        return $abilityMap[$ability] . '_' . \Illuminate\Support\Str::snake(class_basename($class));
    }

    protected function userHasPermission(User $user, string $permission): ?bool
    {
        $permissionNames = app(\Spatie\Permission\PermissionRegistrar::class)
            ->getPermissions()
            ->pluck('name');

        if (! $permissionNames->contains($permission)) {
            return null;
        }

        return $user->hasPermissionTo($permission);
    }

    /**
     * Skip resource authorization in test environments only.
     */
    protected function configureFilamentAuthorization(): void
    {
        if (! $this->app->environment('dusk-testing') && ! env('FILAMENT_SKIP_AUTHORIZATION')) {
            return;
        }

        \Filament\Resources\Resource::skipAuthorization(true);

        $resources = [
            \App\Filament\Resources\AttractionResource::class,
            \App\Filament\Resources\ContactResource::class,
            \App\Filament\Resources\FeedbackResource::class,
            \App\Filament\Resources\GalleryResource::class,
            \App\Filament\Resources\ReservationResource::class,
            \App\Filament\Resources\RoomTypeResource::class,
            \App\Filament\Resources\RoomUnitResource::class,
            \App\Filament\Resources\SettingResource::class,
            \App\Filament\Resources\UserResource::class,
            \App\Filament\Resources\VisitorResource::class,
            \App\Filament\Resources\WhatsappLeadResource::class,
        ];

        foreach ($resources as $resource) {
            $resource::skipAuthorization(true);
        }
    }
}
