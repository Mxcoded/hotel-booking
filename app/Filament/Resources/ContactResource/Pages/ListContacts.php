<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use App\Support\CsvExporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export CSV')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $rows = \App\Models\Contact::query()
                        ->get()
                        ->map(fn (\App\Models\Contact $c) => [
                            'id' => $c->id,
                            'name' => $c->name,
                            'email' => $c->email,
                            'message' => $c->message,
                            'is_read' => $c->is_read ? 'yes' : 'no',
                            'created_at' => $c->created_at?->toDateTimeString(),
                        ])
                        ->collect();

                    return CsvExporter::fromCollection('contact-messages-' . now()->format('Y-m-d') . '.csv', $rows);
                }),
        ];
    }
}
