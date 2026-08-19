<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomMedia extends Model
{
    use HasFactory;

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

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}