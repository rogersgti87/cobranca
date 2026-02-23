<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayableCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'color'
    ];

    /**
     * Empresa a qual a categoria pertence
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Usuário que criou a categoria
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payables()
    {
        return $this->hasMany(Payable::class, 'category_id');
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
}

