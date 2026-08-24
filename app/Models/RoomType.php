<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class RoomType extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'room_type',
        'bed_type',
        'room_size',
        'description',
        'features',
        'base_guests',
        'max_guests',
        'extra_guest_fee',
        'price',
        'weekend_price',
        'holiday_price',
        'seasonal_pricing',
        'discount_percent',
        'discount_start',
        'discount_end',
        'image',
        'min_stay',
        'max_stay',
        'advance_booking_days',
        'check_in_time',
        'check_out_time',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'seasonal_pricing' => 'array',
        'price' => 'float',
        'weekend_price' => 'float',
        'holiday_price' => 'float',
        'extra_guest_fee' => 'float',
        'discount_percent' => 'decimal:2',
        'discount_start' => 'date',
        'discount_end' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'base_guests' => 'integer',
        'max_guests' => 'integer',
        'min_stay' => 'integer',
        'max_stay' => 'integer',
        'advance_booking_days' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────
    
    public function units(): HasMany
    {
        return $this->hasMany(RoomUnit::class)->orderBy('unit_number');
    }

    public function activeUnits(): HasMany
    {
        return $this->units()->where('is_active', true)->where('status', 'available');
    }

    public function media(): HasMany
    {
        return $this->hasMany(RoomMedia::class)->orderBy('sort_order');
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->active();
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('room_type', $type);
    }

    // ── Audit Logging ─────────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['updated_at', 'created_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('room_types')
            ->setDescriptionForEvent(fn (string $eventName) => "Room type {$eventName}");
    }

    // ── Business Logic ───────────────────────────────────────

    /**
     * Get total available units count
     */
    public function getAvailableUnitsCount(): int
    {
        return $this->activeUnits()->count();
    }

    /**
     * Get total units count
     */
    public function getTotalUnitsCount(): int
    {
        return $this->units()->where('is_active', true)->count();
    }

    /**
     * Get effective price for a given date
     */
    public function getPriceForDate(string $date): float
    {
        $dateObj = \Carbon\Carbon::parse($date);

        // 1. Discount period
        if ($this->discount_percent > 0
            && $this->discount_start
            && $this->discount_end
            && $dateObj->between($this->discount_start, $this->discount_end)) {
            return round($this->price * (1 - $this->discount_percent / 100), 2);
        }

        // 2. Holiday price
        if ($this->holiday_price && $this->isHoliday($date)) {
            return $this->holiday_price;
        }

        // 3. Weekend price (Fri=5, Sat=6)
        if ($this->weekend_price && in_array($dateObj->dayOfWeek, [5, 6])) {
            return $this->weekend_price;
        }

        // 4. Seasonal pricing
        if ($this->seasonal_pricing) {
            foreach ($this->seasonal_pricing as $period) {
                if ($dateObj->between($period['start'], $period['end'])) {
                    return $period['price'];
                }
            }
        }

        return $this->price;
    }

    /**
     * Check if date is a holiday
     */
    protected function isHoliday(string $date): bool
    {
        $holidays = [
            '01-01', // New Year
            '12-25', // Christmas
            '12-26', // Boxing Day
            '10-01', // Nigeria Independence
        ];
        return in_array(\Carbon\Carbon::parse($date)->format('m-d'), $holidays);
    }

    /**
     * Check if type has availability for date range.
     *
     * Pass $overridesIndex (from RoomAvailability::indexForRange) so all
     * units are checked against one preloaded map instead of per-night queries.
     */
    public function isAvailableForRange(string $start, string $end, ?array $overridesIndex = null): bool
    {
        $startDate = \Carbon\Carbon::parse($start);
        $endDate = \Carbon\Carbon::parse($end);

        if ($endDate->lt($startDate)) {
            return false;
        }

        $nights = $startDate->diffInDays($endDate);

        if ($nights < $this->min_stay) {
            return false;
        }
        if ($this->max_stay && $nights > $this->max_stay) {
            return false;
        }
        if ($this->advance_booking_days && $startDate->gt(now()->addDays($this->advance_booking_days))) {
            return false;
        }

        // Check if any active unit is available for the range
        $availableUnits = $this->activeUnits()->get();

        foreach ($availableUnits as $unit) {
            if ($unit->isAvailableForRange($start, $end, $overridesIndex)) {
                return true;
            }
        }

        return false;
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

    /**
     * Calculate extra guest fee
     */
    public function calculateExtraGuestFee(int $guestCount): float
    {
        $extra = max(0, $guestCount - $this->base_guests);
        if ($extra > 0 && $this->max_guests && $guestCount > $this->max_guests) {
            $extra = $this->max_guests - $this->base_guests;
        }
        return $extra * $this->extra_guest_fee;
    }

    /**
     * Get max occupancy
     */
    public function getMaxOccupancy(): int
    {
        return $this->max_guests ?? $this->base_guests;
    }
}