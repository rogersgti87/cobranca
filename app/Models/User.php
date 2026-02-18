<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Models\Company;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'image',
        'current_company_id',
        'document',
        'telephone',
        'whatsapp',
        'cep',
        'address',
        'number',
        'complement',
        'district',
        'city',
        'state'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Empresas que o usuário pertence
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Empresa atualmente selecionada pelo usuário
     */
    public function currentCompany()
    {
        return $this->belongsTo(Company::class, 'current_company_id');
    }

    /**
     * Obtém a empresa atual (fallback para primeira empresa se não houver selecionada)
     */
    public function getCurrentCompanyAttribute()
    {
        if ($this->current_company_id) {
            return Company::find($this->current_company_id);
        }
        
        return $this->companies()->first();
    }

    /**
     * Verifica se o usuário pertence a uma empresa específica
     */
    public function belongsToCompany($companyId)
    {
        return $this->companies()->where('company_id', $companyId)->exists();
    }

    /**
     * Define a empresa atual do usuário
     */
    public function switchCompany($companyId)
    {
        if ($this->belongsToCompany($companyId)) {
            $this->current_company_id = $companyId;
            $this->save();
            return true;
        }
        
        return false;
    }

}
