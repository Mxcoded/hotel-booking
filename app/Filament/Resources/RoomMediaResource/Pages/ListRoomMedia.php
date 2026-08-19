<?php

namespace App\Filament\Resources\RoomMediaResource\Pages;

use App\Filament\Resources\RoomMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomMedia extends ListRecords
{
    protected static string $resource = RoomMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
