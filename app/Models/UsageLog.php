<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageLog extends Model
{
    use HasFactory;

    protected $table = 'usage_log';

    protected $fillable = [
        'user_id',
        'pipeline',
        'event_id',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
