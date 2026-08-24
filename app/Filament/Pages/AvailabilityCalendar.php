<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroupEnum;
use App\Models\Reservation;
use App\Models\RoomAvailability;
use App\Models\RoomUnit;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use UnitEnum;

class AvailabilityCalendar extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Availability Calendar';

    protected static UnitEnum|string|null $navigationGroup = NavigationGroupEnum::Reservations;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.availability-calendar';

    protected static ?string $title = 'Availability Calendar';

    /** Max days a shift-click range may span. */
    protected const MAX_RANGE_DAYS = 62;

    public ?int $month = null;

    public ?int $year = null;

    /**
     * Anchor cells for shift-click ranges: [unit_id => ['date' => Y-m-d, 'status' => new status]].
     */
    public array $anchorCell = [];

    public function mount(): void
    {
        $this->month ??= now()->month;
        $this->year ??= now()->year;
    }

    // ── Header Actions ────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markMaintenance')
                ->label('Mark Maintenance')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('gray')
                ->form([
                    Select::make('room_unit_id')
                        ->label('Room unit')
                        ->options($this->unitOptions())
                        ->searchable()
                        ->required(),
                    DatePicker::make('start')
                        ->label('From (first night out of order)')
                        ->default(today())
                        ->minDate(today())
                        ->required(),
                    DatePicker::make('end')
                        ->label('To (last night)')
                        ->default(today())
                        ->minDate(today())
                        ->required(),
                    Textarea::make('note')
                        ->rows(2)
                        ->maxLength(255)
                        ->placeholder('e.g. AC replacement, deep clean…'),
                ])
                ->action(fn (array $data) => $this->applyMaintenance($data)),

            Action::make('clearMaintenance')
                ->label('Clear Maintenance')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->form([
                    Select::make('room_unit_id')
                        ->label('Room unit')
                        ->options($this->unitOptions())
                        ->searchable()
                        ->required(),
                    DatePicker::make('start')->label('From')->default(today())->required(),
                    DatePicker::make('end')->label('To')->default(today())->required(),
                ])
                ->action(fn (array $data) => $this->removeMaintenance($data)),
        ];
    }

    /**
     * Units grouped by room-type name for the maintenance form selects.
     */
    protected function unitOptions(): array
    {
        return $this->unitGroups
            ->mapWithKeys(fn ($units, $typeName) => [
                $typeName => $units
                    ->mapWithKeys(fn (RoomUnit $unit) => [
                        $unit->id => $unit->floor ? "{$unit->unit_number} — Floor {$unit->floor}" : $unit->unit_number,
                    ])
                    ->all(),
            ])
            ->all();
    }

    // ── Navigation ────────────────────────────────────────

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = (int) $date->year;
        $this->month = (int) $date->month;
        $this->anchorCell = [];
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = (int) $date->year;
        $this->month = (int) $date->month;
        $this->anchorCell = [];
    }

    public function goToToday(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
        $this->anchorCell = [];
    }

    /**
     * No-op target for the live poll — re-rendering is the point:
     * bookings made by colleagues appear without a manual reload.
     */
    public function refreshCalendar(): void
    {
    }

    // ── Grid Data ────────────────────────────────────────

    #[Computed]
    public function monthStart(): Carbon
    {
        return Carbon::create($this->year, $this->month, 1)->startOfDay();
    }

    #[Computed]
    public function daysInMonth(): Collection
    {
        return collect(range(0, $this->monthStart->daysInMonth - 1))
            ->map(fn ($i) => $this->monthStart->copy()->addDays($i));
    }

    #[Computed]
    public function yearOptions(): array
    {
        $current = (int) now()->format('Y');

        return collect(range($current - 1, $current + 2))
            ->mapWithKeys(fn ($y) => [$y => $y])
            ->all();
    }

    /**
     * Active units grouped by room-type name for display.
     */
    #[Computed]
    public function unitGroups(): Collection
    {
        return RoomUnit::query()
            ->active()
            ->with('roomType:id,name,sort_order')
            ->get()
            ->sortBy(fn (RoomUnit $unit) => [$unit->roomType->sort_order, $unit->roomType->name, $unit->unit_number])
            ->groupBy(fn (RoomUnit $unit) => $unit->roomType->name);
    }

    /**
     * Blocking overrides for the visible month, keyed [unit_id][Y-m-d].
     * One query, evaluated once per request thanks to #[Computed].
     */
    #[Computed]
    public function overrides(): array
    {
        return RoomAvailability::indexForRange(
            $this->monthStart->toDateString(),
            $this->monthStart->copy()->endOfMonth()->toDateString()
        );
    }

    /**
     * Guest names for booked nights, keyed [unit_id][Y-m-d], so staff can
     * see at a glance who holds each room.
     */
    #[Computed]
    public function bookingLabels(): array
    {
        $reservations = Reservation::query()
            ->confirmed()
            ->whereNotNull('room_unit_id')
            ->where('check_in', '<=', $this->monthStart->copy()->endOfMonth()->toDateString())
            ->where('check_out', '>=', $this->monthStart->toDateString())
            ->get(['id', 'room_unit_id', 'check_in', 'check_out', 'guest_name']);

        $labels = [];

        foreach ($reservations as $reservation) {
            for ($day = $reservation->check_in->copy(); $day->lt($reservation->check_out); $day->addDay()) {
                $labels[$reservation->room_unit_id][$day->toDateString()] = $reservation->guest_name;
            }
        }

        return $labels;
    }

    #[Computed]
    public function stats(): array
    {
        $totalUnits = $this->unitGroups->count();
        $booked = 0;
        $blocked = 0;
        $maintenance = 0;

        foreach ($this->overrides as $dates) {
            foreach ($dates as $status) {
                match ($status) {
                    RoomAvailability::STATUS_BOOKED => $booked++,
                    RoomAvailability::STATUS_BLOCKED => $blocked++,
                    default => $maintenance++,
                };
            }
        }

        $capacity = max(1, $totalUnits * $this->daysInMonth->count());

        return [
            'total_units' => $totalUnits,
            'booked_nights' => $booked,
            'blocked_nights' => $blocked,
            'maintenance_nights' => $maintenance,
            'occupancy_percent' => (int) round(($booked / $capacity) * 100),
        ];
    }

    public function statusFor(int $unitId, Carbon $day): string
    {
        return $this->overrides[$unitId][$day->toDateString()] ?? 'available';
    }

    // ── Cell Presentation ──────────────────────────────────

    /**
     * Standard PMS palette: green = sellable, red = sold, orange = manually
     * held, slate = out of order. Soft fills for editable states, solids
     * for locked ones.
     */
    public function cellClass(int $unitId, Carbon $day): string
    {
        $status = $this->statusFor($unitId, $day);

        if ($day->isPast() && !$day->isToday()) {
            return match ($status) {
                'booked' => 'bg-red-200 text-red-400 cursor-not-allowed',
                default => 'bg-gray-100 text-gray-300 cursor-default dark:bg-gray-800 dark:text-gray-600',
            };
        }

        return match ($status) {
            'booked' => 'bg-red-500 !text-white hover:!bg-red-500 cursor-not-allowed',
            'blocked' => 'bg-orange-400 !text-white hover:!bg-orange-500 cursor-pointer',
            'maintenance' => 'bg-slate-400 !text-white hover:!bg-slate-400 cursor-not-allowed',
            default => ($day->isWeekend()
                    ? 'bg-green-200 hover:bg-green-400'
                    : 'bg-green-100 hover:bg-green-400')
                . ' hover:!text-white cursor-pointer',
        };
    }

    public function cellTitle(int $unitId, Carbon $day): string
    {
        $date = $day->format('D, d M');
        $status = $this->statusFor($unitId, $day);

        return match ($status) {
            'booked' => ($guest = $this->bookingLabels[$unitId][$day->toDateString()] ?? null)
                ? "Booked — {$guest} · {$date}"
                : "Booked — {$date}",
            'blocked' => "Blocked (click to release) — {$date}",
            'maintenance' => "Maintenance — {$date} (use Mark Maintenance above to clear)",
            default => "Available (click to block) — {$date}",
        };
    }

    public function cellLabel(int $unitId, Carbon $day): string
    {
        return str_replace(' — ', ', ', $this->cellTitle($unitId, $day));
    }

    // ── Interaction ────────────────────────────────────────

    /**
     * Plain click toggles a single day; Shift-click after an anchor click
     * applies the anchor's new status to the whole span (range block/unblock).
     */
    public function handleCellClick(int $unitId, string $date, bool $extend = false): void
    {
        if ($extend && isset($this->anchorCell[$unitId])) {
            $anchor = $this->anchorCell[$unitId];
            unset($this->anchorCell[$unitId]);

            $this->applyRange(
                $unitId,
                $anchor['date'],
                $date,
                $anchor['status']
            );

            return;
        }

        $newStatus = $this->setStatusForDay($unitId, $date);

        if ($newStatus === null) {
            return;
        }

        $this->anchorCell[$unitId] = ['date' => $date, 'status' => $newStatus];

        $day = Carbon::createFromFormat('Y-m-d', $date);
        $unitNumber = RoomUnit::find($unitId)?->unit_number ?? "#{$unitId}";

        if ($newStatus === RoomAvailability::STATUS_BLOCKED) {
            Notification::make()
                ->title("{$unitNumber} blocked on {$day->format('d M')}.")
                ->body('Tip: hold Shift and click another date to block a whole stretch.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title("{$unitNumber} released on {$day->format('d M')}.")
                ->success()
                ->send();
        }
    }

    // ── Interaction ────────────────────────────────────────

    /**
     * Apply a target status across an inclusive date span for one unit.
     */
    protected function applyRange(int $unitId, string $startDate, string $endDate, string $targetStatus): void
    {
        if (!Carbon::hasFormat($startDate, 'Y-m-d') || !Carbon::hasFormat($endDate, 'Y-m-d')) {
            return;
        }

        $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $endDate)->startOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        if ($start->diffInDays($end) >= self::MAX_RANGE_DAYS) {
            Notification::make()
                ->title('Range too long.')
                ->body('Select spans of up to ' . self::MAX_RANGE_DAYS . ' days.')
                ->warning()
                ->send();

            return;
        }

        $unitNumber = RoomUnit::find($unitId)?->unit_number ?? "#{$unitId}";

        $changed = 0;
        $skipped = 0;
        $today = now()->startOfDay();

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            if ($day->lt($today)) {
                $skipped++;
                continue;
            }

            $row = RoomAvailability::where('room_unit_id', $unitId)->whereDate('date', $day->toDateString())->first();

            if ($row && in_array($row->status, [RoomAvailability::STATUS_BOOKED, RoomAvailability::STATUS_MAINTENANCE], true)) {
                $skipped++;
                continue;
            }

            if ($targetStatus === RoomAvailability::STATUS_BLOCKED) {
                RoomAvailability::updateOrCreate(
                    ['room_unit_id' => $unitId, 'date' => $day->toDateString()],
                    ['status' => RoomAvailability::STATUS_BLOCKED]
                );
                $changed++;
            } else {
                if ($row) {
                    $row->delete();
                    $changed++;
                }
            }
        }

        if ($changed > 0) {
            $verb = $targetStatus === RoomAvailability::STATUS_BLOCKED ? 'blocked' : 'released';
            $span = $start->format('d M') . ' – ' . $end->format('d M');

            Notification::make()
                ->title("Unit {$unitNumber}: {$changed} night(s) {$verb}.")
                ->body(($skipped > 0 ? "{$skipped} skipped (past / booked / maintenance). " : '') . "Span: {$span}")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Nothing changed.')
                ->body('All selected dates are past, booked or under maintenance.')
                ->warning()
                ->send();
        }
    }

    /**
     * Take a unit out of order for a date range (status = maintenance).
     * Booked nights are never touched; existing blocked rows are upgraded.
     */
    public function applyMaintenance(array $data): void
    {
        $unit = RoomUnit::active()->find($data['room_unit_id'] ?? null);

        if (!$unit) {
            Notification::make()->title('Room unit not found or inactive.')->danger()->send();

            return;
        }

        [$start, $end] = $this->normalisedRange($data['start'] ?? null, $data['end'] ?? null);

        if (!$start || !$end) {
            return;
        }

        $changed = 0;
        $skippedBooked = 0;
        $today = now()->startOfDay();

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            if ($day->lt($today)) {
                continue;
            }

            $row = RoomAvailability::where('room_unit_id', $unit->id)->whereDate('date', $day->toDateString())->first();

            if ($row && $row->status === RoomAvailability::STATUS_BOOKED) {
                $skippedBooked++;
                continue;
            }

            RoomAvailability::updateOrCreate(
                ['room_unit_id' => $unit->id, 'date' => $day->toDateString()],
                [
                    'status' => RoomAvailability::STATUS_MAINTENANCE,
                    'note' => $data['note'] ?? null,
                ]
            );

            $changed++;
        }

        if ($changed > 0) {
            Notification::make()
                ->title("{$unit->unit_number} out of order: {$changed} night(s).")
                ->body(($skippedBooked > 0 ? "{$skippedBooked} booked night(s) left untouched. " : '')
                    . 'Span: ' . $start->format('d M') . ' – ' . $end->format('d M'))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Nothing marked.')
                ->body('The whole range is in the past or already booked.')
                ->warning()
                ->send();
        }
    }

    /**
     * Put a unit back on sale: deletes maintenance rows in the range only —
     * bookings and manual blocks are preserved.
     */
    public function removeMaintenance(array $data): void
    {
        [$start, $end] = $this->normalisedRange($data['start'] ?? null, $data['end'] ?? null);

        if (!$start || !$end) {
            return;
        }

        $unitNumber = RoomUnit::find($data['room_unit_id'] ?? 0)?->unit_number ?? 'Unit';

        $deleted = RoomAvailability::query()
            ->where('room_unit_id', $data['room_unit_id'])
            ->where('status', RoomAvailability::STATUS_MAINTENANCE)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->delete();

        if ($deleted > 0) {
            Notification::make()
                ->title("{$unitNumber} back on sale: {$deleted} night(s) cleared.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('No maintenance nights found in that range.')
                ->warning()
                ->send();
        }
    }

    /**
     * Parse + order a start/end pair, enforcing the same span cap as
     * shift-click ranges. Returns [Carbon|null, Carbon|null].
     *
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    protected function normalisedRange(?string $startDate, ?string $endDate): array
    {
        if (!$startDate || !$endDate || !Carbon::hasFormat($startDate, 'Y-m-d') || !Carbon::hasFormat($endDate, 'Y-m-d')) {
            return [null, null];
        }

        $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $endDate)->startOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        if ($start->diffInDays($end) >= self::MAX_RANGE_DAYS) {
            Notification::make()
                ->title('Range too long.')
                ->body('Select spans of up to ' . self::MAX_RANGE_DAYS . ' days.')
                ->warning()
                ->send();

            return [null, null];
        }

        return [$start, $end];
    }

    /**
     * Toggle one day between available and blocked. Returns the resulting
     * status, or null when nothing changed.
     */
    protected function setStatusForDay(int $unitId, string $date): ?string
    {
        if (!Carbon::hasFormat($date, 'Y-m-d')) {
            return null;
        }

        $day = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();

        if ($day->isPast() && !$day->isToday()) {
            Notification::make()->title('Cannot modify past dates.')->warning()->send();

            return null;
        }

        $unit = RoomUnit::active()->find($unitId);

        if (!$unit) {
            Notification::make()->title('Room unit not found or inactive.')->danger()->send();

            return null;
        }

        $row = RoomAvailability::where('room_unit_id', $unitId)->whereDate('date', $date)->first();

        if ($row && in_array($row->status, [RoomAvailability::STATUS_BOOKED, RoomAvailability::STATUS_MAINTENANCE], true)) {
            Notification::make()
                ->title("{$unit->unit_number} on {$day->format('d M')} is {$row->status} and cannot be changed here.")
                ->warning()
                ->send();

            return null;
        }

        if (!$row || $row->status === RoomAvailability::STATUS_AVAILABLE) {
            RoomAvailability::updateOrCreate(
                ['room_unit_id' => $unitId, 'date' => $date],
                ['status' => RoomAvailability::STATUS_BLOCKED]
            );

            return RoomAvailability::STATUS_BLOCKED;
        }

        $row->delete();

        return RoomAvailability::STATUS_AVAILABLE;
    }
}