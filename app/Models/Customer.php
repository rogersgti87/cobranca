<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'type',
        'document',
        'company',
        'email',
        'email2',
        'status',
        'cep',
        'address',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'phone',
        'whatsapp',
        'payment_method',
        'notification_whatsapp',
        'gateway_pix',
        'gateway_billet',
        'gateway_card',
        'obs',
        'birthdate',
        'notification_email',
        'notificate_5_days',
        'notificate_2_days',
        'notificate_due',
    ];

    /**
     * Empresa a qual o cliente pertence
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Usuário que criou o cliente
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope para filtrar por todas as empresas do usuário autenticado
     */
    public function scopeForUserCompanies($query)
    {
        $companyIds = userCompanyIds();
        
        if (empty($companyIds)) {
            return $query->whereRaw('1 = 0');
        }
        
        return $query->whereIn('company_id', $companyIds);
    }

    /**
     * Scope para clientes ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Ativo');
    }

}
