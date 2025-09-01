<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'file_path',
        'type',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
