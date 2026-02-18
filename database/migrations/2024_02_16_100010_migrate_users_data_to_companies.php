<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $companyData = [
                'name' => $user->company ?? $user->name,
                'type' => $this->determineCompanyType($user->document ?? null),
                'document' => $user->document ?? null,
                'trade_name' => $user->company ?? null,
                'email' => $user->email ?? null,
                'phone' => $user->telephone ?? null,
                'whatsapp' => $user->whatsapp ?? null,
                'cep' => $user->cep ?? null,
                'address' => $user->address ?? null,
                'number' => $user->number ?? null,
                'complement' => $user->complement ?? null,
                'district' => $user->district ?? null,
                'city' => $user->city ?? null,
                'state' => $user->state ?? null,
                'chave_pix' => $user->chave_pix ?? null,
                'token_paghiper' => $user->token_paghiper ?? null,
                'key_paghiper' => $user->key_paghiper ?? null,
                'access_token_paghiper' => $user->access_token_paghiper ?? null,
                'access_token_mp' => $user->access_token_mp ?? null,
                'inter_host' => $user->inter_host ?? null,
                'inter_client_id' => $user->inter_client_id ?? null,
                'inter_client_secret' => $user->inter_client_secret ?? null,
                'inter_scope' => $user->inter_scope ?? null,
                'inter_crt_file' => $user->inter_crt_file ?? null,
                'inter_key_file' => $user->inter_key_file ?? null,
                'inter_crt_file_webhook' => $user->inter_crt_file_webhook ?? null,
                'inter_webhook_url_billet' => $user->inter_webhook_url_billet ?? null,
                'inter_webhook_url_pix' => $user->inter_webhook_url_pix ?? null,
                'inter_chave_pix' => $user->inter_chave_pix ?? null,
                'access_token_inter' => $user->access_token_inter ?? null,
                'use_intermedium' => $user->use_intermedium ?? 'n',
                'environment_asaas' => $user->environment_asaas ?? 'Teste',
                'at_asaas_prod' => $user->at_asaas_prod ?? null,
                'at_asaas_test' => $user->at_asaas_test ?? null,
                'asaas_url_test' => $user->asaas_url_test ?? 'https://sandbox.asaas.com/api/',
                'asaas_url_prod' => $user->asaas_url_prod ?? 'https://api.asaas.com/',
                'api_session_whatsapp' => $user->api_session_whatsapp ?? null,
                'api_token_whatsapp' => $user->api_token_whatsapp ?? $user->api_access_token_whatsapp ?? null,
                'api_status_whatsapp' => $user->api_status_whatsapp ?? null,
                'day_generate_invoice' => $user->day_generate_invoice ?? null,
                'send_generate_invoice' => $user->send_generate_invoice ?? null,
                'typebot_id' => $user->typebot_id ?? null,
                'typebot_enable' => $user->typebot_enable ?? 'n',
                'logo' => $user->image ?? null,
                'status' => $user->status ?? 'Ativo',
                'created_at' => $user->created_at ?? now(),
                'updated_at' => $user->updated_at ?? now(),
            ];

            $companyId = DB::table('companies')->insertGetId($companyData);

            DB::table('company_user')->insert([
                'company_id' => $companyId,
                'user_id' => $user->id,
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')
                ->where('id', $user->id)
                ->update(['current_company_id' => $companyId]);

            $this->migrateRelatedRecords($user->id, $companyId);
        }
    }

    /**
     * Determina o tipo de empresa baseado no documento
     *
     * @param string|null $document
     * @return string
     */
    private function determineCompanyType(?string $document): string
    {
        if (empty($document)) {
            return 'Jurídica';
        }

        $documentClean = preg_replace('/\D/', '', $document);
        
        return strlen($documentClean) === 11 ? 'Física' : 'Jurídica';
    }

    /**
     * Migra os registros relacionados adicionando o company_id
     *
     * @param int $userId
     * @param int $companyId
     * @return void
     */
    private function migrateRelatedRecords(int $userId, int $companyId): void
    {
        $tables = [
            'customers',
            'services',
            'customer_services',
            'invoices',
            'invoice_notifications',
            'payables',
            'payable_categories',
            'suppliers',
            'email_events',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'company_id') && Schema::hasColumn($table, 'user_id')) {
                DB::table($table)
                    ->where('user_id', $userId)
                    ->whereNull('company_id')
                    ->update(['company_id' => $companyId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('company_user')->truncate();
        DB::table('companies')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        DB::table('users')->update(['current_company_id' => null]);
        
        $tables = [
            'customers',
            'services',
            'customer_services',
            'invoices',
            'invoice_notifications',
            'payables',
            'payable_categories',
            'suppliers',
            'email_events',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'company_id')) {
                DB::table($table)->update(['company_id' => null]);
            }
        }
    }
};
