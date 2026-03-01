<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'daily_outlook',
        'upcoming_event_alerts',
        'post_event_reaction',
        'max_alerts_per_day',
        'max_languages',
        'channels_allowed',
        'tags',
        'hashtags',
        'telegram_bot_token',
        'telegram_chat_id',
        'blotato_key',
        'enable_signal_alert',
        'enable_telegram',
        'enable_blotato',
        'enable_email',
    ];

    protected $casts = [
        'daily_outlook' => 'boolean',
        'upcoming_event_alerts' => 'boolean',
        'post_event_reaction' => 'boolean',
        'enable_signal_alert' => 'boolean',
        'channels_allowed' => 'array',
        'price' => 'decimal:2',
        'enable_telegram' => 'boolean',
        'enable_blotato' => 'boolean',
        'enable_email' => 'boolean',
    ];

    /**
     * Relasi ke Subscriptions
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
