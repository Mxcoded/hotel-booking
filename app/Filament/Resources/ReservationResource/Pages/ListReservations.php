<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use App\Support\CsvExporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export CSV')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $rows = \App\Models\Reservation::query()
                        ->with(['roomType', 'roomUnit'])
                        ->get()
                        ->map(fn (\App\Models\Reservation $r) => [
                            'id' => $r->id,
                            'reference' => 'BR-' . str_pad((string) $r->id, 5, '0', STR_PAD_LEFT),
                            'guest_name' => $r->guest_name,
                            'guest_email' => $r->guest_email,
                            'guest_phone' => $r->guest_phone,
                            'room_type' => $r->roomType?->name,
                            'unit_number' => $r->roomUnit?->unit_number,
                            'check_in' => $r->check_in?->toDateString(),
                            'check_out' => $r->check_out?->toDateString(),
                            'nights' => $r->check_in && $r->check_out ? (int) $r->check_in->diffInDays($r->check_out) : '',
                            'guests' => $r->guests,
                            'quoted_total' => (float) $r->quoted_total,
                            'status' => $r->status,
                            'payment_status' => $r->payment_status,
                            'amount_paid' => (float) $r->amount_paid,
                            'discount_amount' => (float) $r->discount_amount,
                            'coupon_code' => $r->coupon_code,
                            'source' => $r->source,
                            'created_at' => $r->created_at?->toDateTimeString(),
                        ])
                        ->collect();

                    return CsvExporter::fromCollection('reservations-' . now()->format('Y-m-d') . '.csv', $rows);
                }),
            Actions\CreateAction::make(),
        ];
    }
}

