<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $fillable =
    [
        'smoke',
        'flame',
        'send_date',
        'humidity',
        'temperature'
    ];

    use HasFactory;
}
