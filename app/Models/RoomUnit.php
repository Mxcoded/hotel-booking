<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class RoomUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'unit_number',
        'floor',
        'status',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'floor' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────
    
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(RoomAvailability::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->active()->where('status', 'available');
    }

    public function scopeOccupied(Builder $query): Builder
    {
        return $query->active()->where('status', 'occupied');
    }

    public function scopeMaintenance(Builder $query): Builder
    {
        return $query->active()->where('status', 'maintenance');
    }

    public function scopeOnFloor(Builder $query, int $floor): Builder
    {
        return $query->where('floor', $floor);
    }

    // ── Status Helpers ────────────────────────────────────────

    public function isAvailable(): bool
    {
        return $this->is_active && $this->status === 'available';
    }

    public function isOccupied(): bool
    {
        return $this->is_active && $this->status === 'occupied';
    }

    public function isMaintenance(): bool
    {
        return $this->is_active && $this->status === 'maintenance';
    }

    public function markOccupied(): void
    {
        $this->update(['status' => 'occupied']);
    }

    public function markAvailable(): void
    {
        $this->update(['status' => 'available']);
    }

    public function markMaintenance(?string $note = null): void
    {
        $this->update([
            'status' => 'maintenance',
            'notes' => $note ?? $this->notes,
        ]);
    }

    // ── Availability Checks ───────────────────────────────────

    /**
     * Check if unit is available for a date range.
     *
     * Pass $overridesIndex (from RoomAvailability::indexForRange) to answer
     * from a preloaded map instead of hitting the database per night.
     */
    public function isAvailableForRange(string $start, string $end, ?array $overridesIndex = null): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $startDate = \Carbon\Carbon::parse($start);
        $endDate = \Carbon\Carbon::parse($end);

        if ($endDate->lt($startDate)) {
            return false;
        }

        // Check min/max stay from room type
        $minStay = $this->roomType->min_stay ?? 1;
        $maxStay = $this->roomType->max_stay;
        $advanceDays = $this->roomType->advance_booking_days ?? 365;

        $nights = $startDate->diffInDays($endDate);
        if ($nights < $minStay) return false;
        if ($maxStay && $nights > $maxStay) return false;
        if ($startDate->gt(now()->addDays($advanceDays))) return false;

        $blocked = $overridesIndex !== null
            ? ($overridesIndex[(int) $this->id] ?? [])
            : RoomAvailability::query()
                ->where('room_unit_id', $this->id)
                ->whereIn('status', RoomAvailability::BLOCKING_STATUSES)
                ->whereBetween('date', [$start, $endDate->copy()->subDay()->toDateString()])
                ->pluck('status', 'date')
                ->all();

        for ($date = $startDate->copy(); $date->lt($endDate); $date->addDay()) {
            $status = $blocked[$date->toDateString()] ?? null;

            if ($status !== null && in_array($status, RoomAvailability::BLOCKING_STATUSES, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get price for a specific date (delegates to room type)
     */
    public function getPriceForDate(string $date): float
    {
        $override = $this->availabilities()
            ->where('date', $date)
            ->value('price_override');
        
        if ($override !== null) {
            return $override;
        }

        return $this->roomType->getPriceForDate($date);
    }

    /**
     * Get total price for a date range
     */
    public function getTotalPriceForRange(string $start, string $end): float
    {
        $startDate = \Carbon\Carbon::parse($start);
        $endDate = \Carbon\Carbon::parse($end);
        $total = 0;

        for ($date = $startDate->copy(); $date->lt($endDate); $date->addDay()) {
            $total += $this->getPriceForDate($date->format('Y-m-d'));
        }

        return $total;
    }
}