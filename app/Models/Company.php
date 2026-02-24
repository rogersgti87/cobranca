<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'document',
        'trade_name',
        'email',
        'phone',
        'whatsapp',
        'cep',
        'address',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'chave_pix',
        'token_paghiper',
        'key_paghiper',
        'access_token_paghiper',
        'access_token_mp',
        'inter_host',
        'inter_client_id',
        'inter_client_secret',
        'inter_scope',
        'inter_crt_file',
        'inter_key_file',
        'inter_crt_file_webhook',
        'inter_webhook_url_billet',
        'inter_webhook_url_pix',
        'inter_chave_pix',
        'access_token_inter',
        'use_intermedium',
        'environment_asaas',
        'at_asaas_prod',
        'at_asaas_test',
        'asaas_url_test',
        'asaas_url_prod',
        'api_session_whatsapp',
        'api_token_whatsapp',
        'api_status_whatsapp',
        'day_generate_invoice',
        'send_generate_invoice',
        'typebot_id',
        'typebot_enable',
        'logo',
        'status',
    ];

    /**
     * Usuários que pertencem a esta empresa
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Clientes da empresa
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Serviços da empresa
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Assinaturas (customer_services) da empresa
     */
    public function customerServices()
    {
        return $this->hasMany(CustomerService::class);
    }

    /**
     * Faturas da empresa
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Contas a pagar da empresa
     */
    public function payables()
    {
        return $this->hasMany(Payable::class);
    }

    /**
     * Fornecedores da empresa
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    /**
     * Categorias de contas a pagar da empresa
     */
    public function payableCategories()
    {
        return $this->hasMany(PayableCategory::class);
    }

    /**
     * Notificações de faturas da empresa
     */
    public function invoiceNotifications()
    {
        return $this->hasMany(InvoiceNotification::class);
    }

    /**
     * Eventos de email da empresa
     */
    public function emailEvents()
    {
        return $this->hasMany(EmailEvent::class);
    }

    /**
     * Scope para empresas ativas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Ativo');
    }

    /**
     * Verifica se o usuário pertence a esta empresa
     */
    public function hasUser($userId)
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    /**
     * Verifica se o usuário é owner da empresa
     */
    public function isOwner($userId)
    {
        return $this->users()
            ->where('user_id', $userId)
            ->wherePivot('role', 'owner')
            ->exists();
    }

    /**
     * Verifica se o usuário é admin ou owner da empresa
     */
    public function isAdminOrOwner($userId)
    {
        return $this->users()
            ->where('user_id', $userId)
            ->whereIn('role', ['admin', 'owner'])
            ->exists();
    }

    /**
     * Retorna informações sobre a validade do certificado Inter
     */
    public function getInterCertificateInfo()
    {
        if (!$this->inter_crt_file || !file_exists(storage_path('/app/' . $this->inter_crt_file))) {
            return [
                'exists' => false,
                'valid' => false,
                'expires_at' => null,
                'days_until_expiration' => null,
                'expired' => false,
                'expires_soon' => false,
            ];
        }

        $certData = openssl_x509_parse(file_get_contents(storage_path('/app/' . $this->inter_crt_file)));
        
        if (!$certData || !isset($certData['validTo_time_t'])) {
            return [
                'exists' => true,
                'valid' => false,
                'expires_at' => null,
                'days_until_expiration' => null,
                'expired' => false,
                'expires_soon' => false,
            ];
        }

        $expiresAt = $certData['validTo_time_t'];
        $now = time();
        $daysUntilExpiration = floor(($expiresAt - $now) / 86400);
        $expired = $expiresAt < $now;
        $expiresSoon = $daysUntilExpiration <= 30 && $daysUntilExpiration >= 0;

        return [
            'exists' => true,
            'valid' => !$expired,
            'expires_at' => $expiresAt,
            'expires_at_formatted' => date('d/m/Y', $expiresAt),
            'days_until_expiration' => $daysUntilExpiration,
            'expired' => $expired,
            'expires_soon' => $expiresSoon,
        ];
    }

    /**
     * Verifica se o certificado Inter está expirado
     */
    public function isInterCertificateExpired()
    {
        $info = $this->getInterCertificateInfo();
        return $info['expired'];
    }

    /**
     * Verifica se o certificado Inter expira em breve (30 dias)
     */
    public function interCertificateExpiresSoon()
    {
        $info = $this->getInterCertificateInfo();
        return $info['expires_soon'];
    }
}
