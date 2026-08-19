<?php

namespace App\Filament\Resources\RoomUnitResource\Pages;

use App\Filament\Resources\RoomUnitResource;
use Filament\Resources\Pages\ListRecords;

class ListRoomUnits extends ListRecords
{
    protected static string $resource = RoomUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}