<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignalConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'pairs',
        'api_keys',
    ];
}
