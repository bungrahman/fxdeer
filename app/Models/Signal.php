<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    use HasFactory;

    protected $fillable = [
        'row_number',
        'signal',
        'pair',
        'price',
        'sl',
        'tp',
        'reason',
        'signal_timestamp',
        'score',
        'stars',
        'conf_level',
        'last_sl',
        'last_tp',
        'result',
    ];
}
