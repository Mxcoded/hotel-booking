<?php

namespace App\Filament\Resources\FeedbackResource\Pages;

use App\Filament\Resources\FeedbackResource;
use App\Support\CsvExporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeedback extends ListRecords
{
    protected static string $resource = FeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export CSV')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $rows = \App\Models\Feedback::query()
                        ->get()
                        ->map(fn (\App\Models\Feedback $f) => [
                            'id' => $f->id,
                            'name' => $f->name,
                            'email' => $f->email,
                            'rating' => $f->rating,
                            'message' => $f->message,
                            'is_read' => $f->is_read ? 'yes' : 'no',
                            'is_approved' => $f->is_approved ? 'yes' : 'no',
                            'created_at' => $f->created_at?->toDateTimeString(),
                        ])
                        ->collect();

                    return CsvExporter::fromCollection('feedback-' . now()->format('Y-m-d') . '.csv', $rows);
                }),
        ];
    }
}
