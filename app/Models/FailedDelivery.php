<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'pipeline',
        'channel',
        'error_message',
        'payload',
        'retry_count',
    ];

    protected $casts = [
        'payload' => 'json',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
