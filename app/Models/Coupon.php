<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Coupon extends Model
{
    use LogsActivity;

    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_booking_amount',
        'valid_from',
        'valid_until',
        'max_uses',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_booking_amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['updated_at', 'created_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('coupons')
            ->setDescriptionForEvent(fn (string $eventName) => "Coupon {$eventName}");
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function hasReachedUseLimit(): bool
    {
        return $this->max_uses !== null && $this->used_count >= $this->max_uses;
    }

    public function isExpired(): bool
    {
        if ($this->valid_until && now()->gt($this->valid_until)) {
            return true;
        }
        if ($this->valid_from && now()->lt($this->valid_from)) {
            return true;
        }
        return false;
    }

    /**
     * Compute the discount for a subtotal. Returns [valid: bool, discount: float, reason: ?string].
     */
    public function discountFor(float $subtotal): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'discount' => 0, 'reason' => 'This code is no longer active.'];
        }
        if ($this->isExpired()) {
            return ['valid' => false, 'discount' => 0, 'reason' => 'This code has expired.'];
        }
        if ($this->hasReachedUseLimit()) {
            return ['valid' => false, 'discount' => 0, 'reason' => 'This code has reached its usage limit.'];
        }
        if ((float) $this->min_booking_amount > 0 && $subtotal < (float) $this->min_booking_amount) {
            return ['valid' => false, 'discount' => 0, 'reason' => 'This code requires a minimum booking value.'];
        }

        $discount = $this->discount_type === self::TYPE_PERCENT
            ? round($subtotal * ((float) $this->discount_value / 100), 2)
            : round(min((float) $this->discount_value, $subtotal), 2);

        return ['valid' => true, 'discount' => $discount, 'reason' => null];
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}
