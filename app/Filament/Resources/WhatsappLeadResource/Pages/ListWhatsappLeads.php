<?php

namespace App\Filament\Resources\WhatsappLeadResource\Pages;

use App\Filament\Resources\WhatsappLeadResource;
use App\Support\CsvExporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhatsappLeads extends ListRecords
{
    protected static string $resource = WhatsappLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export CSV')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $rows = \App\Models\WhatsappLead::query()
                        ->get()
                        ->map(fn (\App\Models\WhatsappLead $l) => [
                            'id' => $l->id,
                            'name' => $l->name,
                            'phone' => $l->phone,
                            'ip_address' => $l->ip_address,
                            'created_at' => $l->created_at?->toDateTimeString(),
                        ])
                        ->collect();

                    return CsvExporter::fromCollection('whatsapp-leads-' . now()->format('Y-m-d') . '.csv', $rows);
                }),
        ];
    }
}
