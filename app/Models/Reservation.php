<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Reservation extends Model
{
    use LogsActivity;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CHECKED_IN = 'checked_in';
    public const STATUS_CHECKED_OUT = 'checked_out';
    public const STATUS_NO_SHOW = 'no_show';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PARTIAL = 'partial';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REFUNDED = 'refunded';

    public const STATUS_OPTIONS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_CHECKED_IN => 'Checked in',
        self::STATUS_CHECKED_OUT => 'Checked out',
        self::STATUS_NO_SHOW => 'No show',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    protected static bool $logUnguarded = false;

    protected $attributes = [
        'payment_status' => self::PAYMENT_UNPAID,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'status', 'guest_name', 'guest_email', 'guest_phone',
                'check_in', 'check_out', 'guests', 'quoted_total',
                'payment_status', 'amount_paid', 'payment_method',
                'payment_reference', 'coupon_code', 'discount_amount',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('reservations')
            ->setDescriptionForEvent(fn (string $eventName) => "Booking request {$eventName}");
    }

    protected $fillable = [
        'room_type_id',
        'room_unit_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in',
        'check_out',
        'guests',
        'quoted_total',
        'status',
        'special_requests',
        'source',
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'no_show_at',
        'payment_status',
        'amount_paid',
        'payment_method',
        'payment_reference',
        'paid_at',
        'coupon_code',
        'discount_amount',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'guests' => 'integer',
        'quoted_total' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'no_show_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $reservation) {
            $reservation->syncUnitBooking();
        });

        static::deleting(function (self $reservation) {
            $reservation->releaseUnitBooking();
        });
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function roomUnit(): BelongsTo
    {
        return $this->belongsTo(RoomUnit::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeActiveStay($query)
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN]);
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('status', self::STATUS_CHECKED_IN);
    }

    public function scopeCheckedOut($query)
    {
        return $query->where('status', self::STATUS_CHECKED_OUT);
    }

    public function scopeNoShow($query)
    {
        return $query->where('status', self::STATUS_NO_SHOW);
    }

    public function scopeArriving($query)
    {
        return $query->where('check_in', now()->toDateString())
            ->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_PENDING]);
    }

    public function scopeLeaving($query)
    {
        return $query->where('check_out', now()->toDateString())
            ->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN]);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('payment_status', [self::PAYMENT_UNPAID, self::PAYMENT_PARTIAL]);
    }

    // ── Status Transitions ────────────────────────────────────

    public function markConfirmed(): bool
    {
        return DB::transaction(function () {
            $in = $this->check_in?->copy()->startOfDay();
            $out = $this->check_out?->copy()->startOfDay();

            if (!$in || !$out || $out->lte($in)) {
                return false;
            }

            // Assign a unit if possible: reuse the currently assigned one when it
            // can still hold the stay, otherwise find a fresh bookable unit. Never
            // confirm a reservation that has no physical room to occupy.
            $unitId = $this->usableUnitId() ?? optional($this->findBookableUnit($in, $out))->id;

            if (!$unitId) {
                return false;
            }

            $saved = $this->update([
                'status' => self::STATUS_CONFIRMED,
                'confirmed_at' => now(),
                'room_unit_id' => $unitId,
            ]);

            // syncUnitBooking (see booted) marks the assigned unit's nights as booked.
            if ($saved && filled($this->guest_email)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($this->guest_email)
                        ->queue(new \App\Mail\GuestBookingConfirmed($this->fresh()));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to queue guest confirmation email: ' . $e->getMessage());
                }
            }

            return (bool) $saved;
        });
    }

    public function markCancelled(): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'confirmed_at' => null,
        ]);
    }

    /**
     * Mark the guest as having checked in. Only valid from confirmed/pending.
     * The assigned unit is marked occupied on the front desk.
     */
    public function markCheckedIn(): bool
    {
        $updated = $this->update([
            'status' => self::STATUS_CHECKED_IN,
            'checked_in_at' => now(),
        ]);

        if ($updated && $this->room_unit_id) {
            $unit = $this->roomUnit;
            if ($unit && $unit->is_active) {
                $unit->markOccupied();
            }
        }

        return (bool) $updated;
    }

    /**
     * Mark the guest as checked out. The unit is released back to available
     * and its booked nights kept for history.
     */
    public function markCheckedOut(): bool
    {
        $updated = $this->update([
            'status' => self::STATUS_CHECKED_OUT,
            'checked_out_at' => now(),
        ]);

        if ($updated && $this->room_unit_id) {
            $unit = $this->roomUnit;
            if ($unit && $unit->status === 'occupied') {
                $unit->markAvailable();
            }
        }

        return (bool) $updated;
    }

    /**
     * Mark a confirmed guest who never arrived as a no-show.
     */
    public function markNoShow(): bool
    {
        return $this->update([
            'status' => self::STATUS_NO_SHOW,
            'no_show_at' => now(),
        ]);
    }

    // ── Payment Helpers ──────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function hasPartialPayment(): bool
    {
        return $this->payment_status === self::PAYMENT_PARTIAL;
    }

    public function balanceDue(): float
    {
        return round(max(0, (float) $this->quoted_total - (float) $this->amount_paid), 2);
    }

    /**
     * Record a payment on this reservation. When the running total reaches
     * or exceeds the quoted total the payment is marked fully paid.
     */
    public function recordPayment(float $amount, ?string $method = null, ?string $reference = null): bool
    {
        return $this->update([
            'amount_paid' => round((float) $this->amount_paid + $amount, 2),
            'payment_method' => $method ?? $this->payment_method,
            'payment_reference' => $reference ?? $this->payment_reference,
            'payment_status' => ($this->amount_paid + $amount) >= (float) $this->quoted_total
                ? self::PAYMENT_PAID
                : self::PAYMENT_PARTIAL,
            'paid_at' => now(),
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    // ── Unit Booking Sync ─────────────────────────────────────

    /**
     * Keep the calendar in step with the reservation state: confirmed
     * stays hold their assigned unit's nights as "booked" rows,
     * everything else releases them.
     */
    public function syncUnitBooking(): void
    {
        if (!$this->exists || !$this->room_type_id || !$this->check_in || !$this->check_out) {
            return;
        }

        if (!in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN], true)) {
            $this->releaseUnitBooking();

            return;
        }

        DB::transaction(function () {
            $in = $this->check_in->copy()->startOfDay();
            $out = $this->check_out->copy()->startOfDay();

            if ($out->lte($in)) {
                $this->releaseUnitBooking();

                return;
            }

            $unitId = $this->usableUnitId();

            if (!$unitId) {
                $this->releaseUnitBooking();
                $fresh = $this->findBookableUnit($in, $out);

                if ($fresh) {
                    $unitId = $fresh->id;
                    $this->newQueryWithoutScopes()
                        ->whereKey($this->getKey())
                        ->update(['room_unit_id' => $unitId]);
                    $this->room_unit_id = $unitId;
                    $this->syncOriginal();
                } else {
                    return;
                }
            }

            $note = $this->bookingNote();

            for ($date = $in->copy(); $date->lt($out); $date->addDay()) {
                RoomAvailability::updateOrCreate(
                    ['room_unit_id' => $unitId, 'date' => $date->toDateString()],
                    ['status' => 'booked', 'note' => $note]
                );
            }
        });
    }

    /**
     * Remove the booked rows this reservation wrote, using the original
     * stay window so date edits clean up after themselves.
     */
    public function releaseUnitBooking(): void
    {
        $unitId = $this->getOriginal('room_unit_id') ?? $this->room_unit_id;
        $in = $this->getOriginal('check_in') ?? $this->check_in;
        $out = $this->getOriginal('check_out') ?? $this->check_out;

        if (!$unitId || !$in || !$out) {
            return;
        }

        RoomAvailability::query()
            ->where('room_unit_id', $unitId)
            ->where('status', 'booked')
            ->where('note', $this->bookingNote())
            ->whereBetween('date', [
                \Illuminate\Support\Carbon::parse($in)->startOfDay(),
                \Illuminate\Support\Carbon::parse($out)->copy()->subDay()->endOfDay(),
            ])
            ->delete();
    }

    protected function bookingNote(): string
    {
        return "Booking #{$this->getKey()}";
    }

    /**
     * The currently assigned unit, if it can still legally hold this stay.
     */
    protected function usableUnitId(): ?int
    {
        if (!$this->room_unit_id) {
            return null;
        }

        $unit = RoomUnit::active()
            ->where('status', 'available')
            ->find($this->room_unit_id);

        if (!$unit) {
            return null;
        }

        return $this->unitIsFree($unit, $this->check_in, $this->check_out) ? (int) $unit->id : null;
    }

    /**
     * First active unit of this type that has no blocking availability row
     * and no overlapping confirmed reservation for every night of the stay.
     */
    protected function findBookableUnit($in, $out): ?RoomUnit
    {
        return RoomUnit::active()
            ->where('room_type_id', $this->room_type_id)
            ->where('status', 'available')
            ->orderBy('unit_number')
            ->get()
            ->first(fn (RoomUnit $unit) => $this->unitIsFree($unit, $in, $out));
    }

    protected function unitIsFree(RoomUnit $unit, $in, $out): bool
    {
        $blocked = RoomAvailability::query()
            ->where('room_unit_id', $unit->id)
            ->whereIn('status', ['booked', 'blocked', 'maintenance'])
            ->whereBetween('date', [
                \Illuminate\Support\Carbon::parse($in)->toDateString(),
                \Illuminate\Support\Carbon::parse($out)->copy()->subDay()->toDateString(),
            ]);

        if ($this->room_unit_id === $unit->id) {
            $blocked->where(function ($q) {
                $q->whereNull('note')->orWhere('note', '!=', $this->bookingNote());
            });
        }

        if ($blocked->where(function ($q) {
            $q->whereNull('note')->orWhere('note', '!=', $this->bookingNote());
        })->exists()) {
            return false;
        }

        $overlap = self::query()
            ->where('room_unit_id', $unit->id)
            ->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN])
            ->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()))
            ->where('check_in', '<', \Illuminate\Support\Carbon::parse($out)->toDateString())
            ->where('check_out', '>', \Illuminate\Support\Carbon::parse($in)->toDateString());

        return !$overlap->exists();
    }
}
