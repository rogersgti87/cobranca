<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerService extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'customer_id',
        'service_id',
        'description',
        'day_due',
        'price',
        'period',
        'status',
        'gateway_payment',
        'payment_method',
        'start_billing',
        'end_billing',
        'tax',
        'tax_percent',
    ];

    /**
     * Empresa a qual a assinatura pertence
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Usuário que criou a assinatura
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cliente da assinatura
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Serviço da assinatura
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope para assinaturas ativas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Ativo');
    }

}
