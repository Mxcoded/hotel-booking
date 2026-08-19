<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAvailability extends Model
{
    use HasFactory;

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
}