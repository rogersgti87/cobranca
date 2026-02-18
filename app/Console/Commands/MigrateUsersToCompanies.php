<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateUsersToCompanies extends Command
{
    protected $signature = 'migrate:users-to-companies 
                            {--fresh : Limpar dados existentes antes de migrar}
                            {--dry-run : Apenas simular sem executar}';

    protected $description = 'Migra dados de usuários para a tabela companies';

    public function handle()
    {
        $this->info('=== MIGRAÇÃO DE USERS PARA COMPANIES ===');
        $this->newLine();

        if (!$this->validateTables()) {
            return Command::FAILURE;
        }

        if ($this->option('fresh')) {
            if ($this->confirm('Isso irá limpar todos os dados das tabelas companies e company_user. Deseja continuar?', false)) {
                $this->cleanTables();
            } else {
                $this->warn('Operação cancelada.');
                return Command::SUCCESS;
            }
        }

        $users = DB::table('users')->get();
        
        if ($users->isEmpty()) {
            $this->warn('Nenhum usuário encontrado para migrar.');
            return Command::SUCCESS;
        }

        $this->info("Encontrados {$users->count()} usuário(s) para migrar.");
        $this->newLine();

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $migratedCount = 0;
        $errors = [];

        foreach ($users as $user) {
            try {
                if (!$this->option('dry-run')) {
                    $this->migrateUser($user);
                }
                $migratedCount++;
            } catch (\Exception $e) {
                $errors[] = "Erro ao migrar {$user->name}: {$e->getMessage()}";
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($this->option('dry-run')) {
            $this->info("✓ SIMULAÇÃO: {$migratedCount} empresa(s) seriam criada(s).");
        } else {
            $this->info("✓ Migração concluída! {$migratedCount} empresa(s) criada(s).");
            $this->showMigrationSummary();
        }

        if (!empty($errors)) {
            $this->newLine();
            $this->error('Erros encontrados:');
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
        }

        return Command::SUCCESS;
    }

    private function validateTables(): bool
    {
        $this->info('1. Validando estrutura do banco de dados...');

        if (!Schema::hasTable('companies')) {
            $this->error('   ✗ Tabela companies não existe. Execute as migrations primeiro.');
            return false;
        }

        if (!Schema::hasTable('company_user')) {
            $this->error('   ✗ Tabela company_user não existe. Execute as migrations primeiro.');
            return false;
        }

        $requiredColumns = [
            'at_asaas_prod', 'at_asaas_test', 'environment_asaas',
            'token_paghiper', 'key_paghiper', 'access_token_mp',
            'api_session_whatsapp', 'api_token_whatsapp', 'typebot_id'
        ];

        $missingColumns = [];
        foreach ($requiredColumns as $column) {
            if (!Schema::hasColumn('users', $column)) {
                $missingColumns[] = $column;
            }
        }

        if (!empty($missingColumns)) {
            $this->warn('   ⚠ Alguns campos não existem na tabela users:');
            foreach ($missingColumns as $col) {
                $this->line("     - {$col}");
            }
            $this->line('   Os dados desses campos serão definidos como NULL.');
        } else {
            $this->info('   ✓ Todos os campos necessários existem.');
        }

        $this->newLine();
        return true;
    }

    private function cleanTables(): void
    {
        $this->info('2. Limpando tabelas...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('company_user')->truncate();
        DB::table('companies')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        if (Schema::hasColumn('users', 'current_company_id')) {
            DB::table('users')->update(['current_company_id' => null]);
        }

        $this->info('   ✓ Tabelas limpas com sucesso.');
        $this->newLine();
    }

    private function migrateUser($user): void
    {
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

        if (Schema::hasColumn('users', 'current_company_id')) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['current_company_id' => $companyId]);
        }
    }

    private function determineCompanyType(?string $document): string
    {
        if (empty($document)) {
            return 'Jurídica';
        }

        $documentClean = preg_replace('/\D/', '', $document);
        
        return strlen($documentClean) === 11 ? 'Física' : 'Jurídica';
    }

    private function showMigrationSummary(): void
    {
        $this->newLine();
        $this->info('=== RESUMO DA MIGRAÇÃO ===');
        $this->newLine();

        $companies = DB::table('companies')->get();
        
        $table = [];
        foreach ($companies as $company) {
            $table[] = [
                $company->id,
                $company->name,
                $company->document ?? 'N/A',
                $company->at_asaas_prod ? '✓ Configurado' : '✗ Não configurado',
                $company->token_paghiper ? '✓ Configurado' : '✗ Não configurado',
                $company->api_session_whatsapp ? '✓ Configurado' : '✗ Não configurado',
            ];
        }

        $this->table(
            ['ID', 'Nome', 'Documento', 'Asaas', 'PagHiper', 'WhatsApp'],
            $table
        );

        $this->newLine();
        $this->info('Próximo passo: Execute "php artisan migrate" para finalizar.');
    }
}
