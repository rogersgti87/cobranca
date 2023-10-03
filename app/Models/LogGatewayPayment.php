<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogGatewayPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'log',
    ];

}
