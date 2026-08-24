<?php

namespace App\Enums;

use Filament\Navigation\NavigationGroup;

enum NavigationGroupEnum: string
{
    case HotelManagement = 'Hotel Management';
    case GuestCommunications = 'Guest Communications';
    case Settings = 'Settings';
    case Reservations = 'Reservations';

    public function getGroup(): NavigationGroup
    {
        return NavigationGroup::make()
            ->label($this->value)
            ->icon(match ($this) {
                self::HotelManagement => 'heroicon-o-building-office-2',
                self::GuestCommunications => 'heroicon-o-chat-bubble-left-right',
                self::Settings => 'heroicon-o-cog-6-tooth',
                self::Reservations => 'heroicon-o-calendar-days',
            });
    }
}