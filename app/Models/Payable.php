<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
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

    /**
     * Empresa a qual a conta pertence
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Usuário que criou a conta
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(PayableCategory::class);
    }

    public function reversals()
    {
        return $this->hasMany(PayableReversal::class);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('payables.company_id', $companyId);
    }

    /**
     * Scope para contas pendentes
     */
    public function scopePendente($query)
    {
        return $query->where('status', 'Pendente');
    }

    /**
     * Scope para contas pagas
     */
    public function scopePago($query)
    {
        return $query->where('status', 'Pago');
    }
}
