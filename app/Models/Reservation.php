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
    public const STATUS_CANCELLED = 'cancelled';

    protected static bool $logUnguarded = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'guest_name', 'guest_email', 'guest_phone', 'check_in', 'check_out', 'guests', 'quoted_total'])
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
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'guests' => 'integer',
        'quoted_total' => 'decimal:2',
        'confirmed_at' => 'datetime',
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

    // ── Status Transitions ────────────────────────────────────

    public function markConfirmed(): bool
    {
        $ok = $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        return $ok && $this->room_unit_id !== null;
    }

    public function markCancelled(): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'confirmed_at' => null,
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

        if ($this->status !== self::STATUS_CONFIRMED) {
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
            ->where('status', self::STATUS_CONFIRMED)
            ->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()))
            ->where('check_in', '<', \Illuminate\Support\Carbon::parse($out)->toDateString())
            ->where('check_out', '>', \Illuminate\Support\Carbon::parse($in)->toDateString());

        return !$overlap->exists();
    }
}
