<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Attraction extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'category',
        'description',
        'image',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['updated_at', 'created_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('attractions')
            ->setDescriptionForEvent(fn (string $eventName) => "Attraction {$eventName}");
    }
}
