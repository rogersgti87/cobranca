<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'event',
        'email',
        'identification',
        'date',
        'message_id',
        'subject',
        'tag',
        'sending_ip',
        'ts_epoch',
        'link',
        'reason',
    ];

    /**
     * Empresa a qual o evento pertence
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Usuário relacionado ao evento
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
}
