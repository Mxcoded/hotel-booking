<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class HousekeepingLog extends Model
{
    use LogsActivity;

    public const STATUS_DIRTY = 'dirty';
    public const STATUS_CLEAN = 'clean';
    public const STATUS_INSPECTED = 'inspected';

    public const STATUS_OPTIONS = [
        self::STATUS_DIRTY => 'Dirty',
        self::STATUS_CLEAN => 'Clean',
        self::STATUS_INSPECTED => 'Inspected',
    ];

    protected $fillable = [
        'room_unit_id',
        'status',
        'note',
        'assigned_to',
        'cleaned_at',
    ];

    protected $casts = [
        'cleaned_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['updated_at', 'created_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('housekeeping')
            ->setDescriptionForEvent(fn (string $eventName) => "Housekeeping log {$eventName}");
    }

    public function roomUnit(): BelongsTo
    {
        return $this->belongsTo(RoomUnit::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeDirty($query)
    {
        return $query->where('status', self::STATUS_DIRTY);
    }

    public function scopeClean($query)
    {
        return $query->where('status', self::STATUS_CLEAN);
    }
}
