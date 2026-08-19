<?php

namespace App\Filament\Resources\WhatsappLeadResource\Pages;

use App\Filament\Resources\WhatsappLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhatsappLeads extends ListRecords
{
    protected static string $resource = WhatsappLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
