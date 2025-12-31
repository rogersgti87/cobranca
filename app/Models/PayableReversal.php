<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayableReversal extends Model
{
    use HasFactory;

    protected $fillable = [
        'payable_id',
        'user_id',
        'reversed_at',
        'reversal_reason'
    ];

    protected $casts = [
        'reversed_at' => 'datetime',
    ];

    public function payable()
    {
        return $this->belongsTo(Payable::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

