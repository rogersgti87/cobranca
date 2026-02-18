<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
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
        'obs'
    ];

    /**
     * Empresa a qual o fornecedor pertence
     */
    public function companyRelation()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Usuário que criou o fornecedor
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
     * Scope para fornecedores ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Ativo');
    }
}

