<?php

namespace App\Providers;

use Filament\Resources\Resource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('dusk-testing') || env('FILAMENT_SKIP_AUTHORIZATION')) {
            \Log::info('SKIP AUTHORIZATION ENABLED in ' . __METHOD__);
            \Filament\Resources\Resource::skipAuthorization(true);
            
            // Explicitly call on all known resource classes
            $resources = [
                \App\Filament\Resources\AttractionResource::class,
                \App\Filament\Resources\ContactResource::class,
                \App\Filament\Resources\FeedbackResource::class,
                \App\Filament\Resources\GalleryResource::class,
                \App\Filament\Resources\ReservationResource::class,
                \App\Filament\Resources\RoomTypeResource::class,
                \App\Filament\Resources\RoomUnitResource::class,
                \App\Filament\Resources\SettingResource::class,
                \App\Filament\Resources\VisitorResource::class,
                \App\Filament\Resources\WhatsappLeadResource::class,
            ];
            
            foreach ($resources as $resource) {
                $resource::skipAuthorization(true);
            }
        }
    }
}
