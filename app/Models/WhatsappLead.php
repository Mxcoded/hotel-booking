<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'ip_address',
    ];
}
