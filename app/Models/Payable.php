<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supplier_id',
        'category_id',
        'description',
        'price',
        'type', // Fixa, Recorrente, Parcelada
        'payment_method',
        'date_due',
        'date_payment',
        'status', // Pendente, Pago, Cancelado
        'recurrence_period', // Mensal, Semanal, etc
        'recurrence_day',
        'recurrence_end',
        'installments',
        'installment_number',
        'parent_id'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(PayableCategory::class);
    }
}
