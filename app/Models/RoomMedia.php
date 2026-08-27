<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class RoomMedia extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'room_type_id',
        'file_path',
        'type',
        'sort_order',
        'caption',
        'alt_text',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['updated_at', 'created_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('room_media')
            ->setDescriptionForEvent(fn (string $eventName) => "Room media {$eventName}");
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}