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
     * Allow admin/super_admin through all Gate checks.
     * Without Policies registered, Gate::allows('viewAny', Model) returns false
     * for every resource, causing 403 on all admin pages.
     */
    protected function configureGate(): void
    {
        Gate::before(function (User $user, string $ability) {
            if ($user instanceof \App\Models\User && $user->hasAnyRole(['super_admin', 'admin'])) {
                return true;
            }

            return null;
        });
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
