<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistry extends Model
{
    use HasFactory;

    protected $table = 'event_registry';
    protected $primaryKey = 'event_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'event_id',
        'pipeline',
        'event_time_utc',
        'sent_at',
        'language',
        'channel',
    ];

    protected $casts = [
        'event_time_utc' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
