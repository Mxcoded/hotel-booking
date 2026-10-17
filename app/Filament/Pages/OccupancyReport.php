<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroupEnum;
use App\Models\Reservation;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use App\Models\RoomUnit;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use UnitEnum;

class OccupancyReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Occupancy & Revenue';

    protected static UnitEnum|string|null $navigationGroup = NavigationGroupEnum::Reservations;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.occupancy-report';

    protected static ?string $title = 'Occupancy & Revenue Report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(['month' => now()->format('Y-m')]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DatePicker::make('month')
                    ->label('Report month')
                    ->format('Y-m')
                    ->displayFormat('F Y')
                    ->default(now()->format('Y-m'))
                    ->live(),
            ])
            ->statePath('data');
    }

    public function getMonth(): string
    {
        $value = $this->data['month'] ?? now()->format('Y-m');

        // The DatePicker may submit a full date (e.g. 2026-08-28) rather than a
        // clean Y-m, so normalise it before parsing.
        if (preg_match('/^\d{4}-\d{2}/', (string) $value, $m)) {
            return $m[0];
        }

        return now()->format('Y-m');
    }

    public function getReport(): array
    {
        $start = Carbon::createFromFormat('Y-m', $this->getMonth())->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $activeUnits = RoomUnit::active()->count();
        $totalNights = $activeUnits * $start->daysInMonth;

        $bookedNights = RoomAvailability::query()
            ->where('status', RoomAvailability::STATUS_BOOKED)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->count();

        $occupancy = $totalNights > 0 ? round(($bookedNights / $totalNights) * 100, 1) : 0;

        // Reservations overlapping the month with an actual stay (not cancelled/no-show).
        $reservations = Reservation::query()
            ->whereNotIn('status', [Reservation::STATUS_CANCELLED, Reservation::STATUS_NO_SHOW])
            ->where('check_in', '<=', $end->toDateString())
            ->where('check_out', '>=', $start->toDateString())
            ->with('roomType')
            ->get();

        $revenuePotential = $reservations->sum(fn ($r) => (float) $r->quoted_total);
        $collected = $reservations->sum(fn ($r) => (float) $r->amount_paid);

        $byRoomType = $reservations
            ->groupBy(fn ($r) => $r->roomType?->name ?? 'Unknown')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'revenue' => round($group->sum(fn ($r) => (float) $r->quoted_total), 2),
            ])
            ->sortByDesc('revenue')
            ->take(10);

        return [
            'month_label' => $start->format('F Y'),
            'active_units' => $activeUnits,
            'total_nights' => $totalNights,
            'booked_nights' => $bookedNights,
            'occupancy' => $occupancy,
            'bookings' => $reservations->count(),
            'revenue_potential' => round($revenuePotential, 2),
            'collected' => round($collected, 2),
            'by_room_type' => $byRoomType,
        ];
    }
}
