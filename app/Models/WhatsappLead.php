<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class WhatsappLead extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'phone',
        'ip_address',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['updated_at', 'created_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('whatsapp_leads')
            ->setDescriptionForEvent(fn (string $eventName) => "WhatsApp lead {$eventName}");
    }
}
