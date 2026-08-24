<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAvailability extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_BOOKED = 'booked';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_MAINTENANCE = 'maintenance';

    public const BLOCKING_STATUSES = [
        self::STATUS_BOOKED,
        self::STATUS_BLOCKED,
        self::STATUS_MAINTENANCE,
    ];

    protected $fillable = [
        'room_unit_id',
        'date',
        'status',
        'price_override',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'price_override' => 'decimal:2',
    ];

    public function roomUnit(): BelongsTo
    {
        return $this->belongsTo(RoomUnit::class);
    }

    /**
     * Load every blocking override in a date window with a single query,
     * indexed as [unit_id][Y-m-d] => status for O(1) range lookups.
     */
    public static function indexForRange(string $start, string $end): array
    {
        return static::query()
            ->whereIn('status', self::BLOCKING_STATUSES)
            ->whereBetween('date', [$start, $end])
            ->get(['room_unit_id', 'date', 'status'])
            ->groupBy('room_unit_id')
            ->mapWithKeys(function ($rows, $unitId) {
                $dates = $rows->mapWithKeys(fn (self $row) => [
                    $row->date instanceof \Carbon\Carbon ? $row->date->toDateString() : (string) $row->date => $row->status,
                ]);

                return [(int) $unitId => $dates->all()];
            })
            ->all();
    }
}
