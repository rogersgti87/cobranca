<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Payable;
use App\Models\Supplier;
use App\Models\PayableCategory;
use DB;

class MigrateToMulticompany extends Command
{
    protected $signature = 'migrate:multicompany {--dry-run : Executar sem fazer alterações} {--force : Forçar execução sem confirmação}';
    
    protected $description = 'Migra dados existentes para o sistema multiempresa';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        
        if ($dryRun) {
            $this->warn('MODO DRY-RUN: Nenhuma alteração será feita no banco de dados');
        } else {
            if (!$force && !$this->confirm('ATENÇÃO: Este comando irá modificar o banco de dados. Você fez backup? Deseja continuar?')) {
                $this->info('Operação cancelada.');
                return 0;
            }
        }
        
        if (!$dryRun) {
            DB::beginTransaction();
        }
        
        try {
            $users = User::all();
            $totalUsers = $users->count();
            
            $this->info("Processando {$totalUsers} usuário(s)...\n");
            
            $bar = $this->output->createProgressBar($totalUsers);
            
            foreach ($users as $user) {
                $this->newLine();
                $this->info("Usuário: {$user->name} (ID: {$user->id})");
                
                // Verificar se já existe empresa para este usuário
                $existingCompany = Company::whereHas('users', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->first();
                
                if ($existingCompany) {
                    $this->warn("  ⚠ Usuário já possui empresa: {$existingCompany->name}");
                    $company = $existingCompany;
                } else {
                    // Criar empresa para o usuário baseado nos dados dele
                    $companyData = [
                        'name' => $user->company ?? $user->name,
                        'document' => $user->document,
                        'type' => 'Jurídica',
                        'email' => $user->email,
                        'phone' => $user->telephone,
                        'whatsapp' => $user->whatsapp,
                        'cep' => $user->cep,
                        'address' => $user->address,
                        'number' => $user->number,
                        'complement' => $user->complement,
                        'district' => $user->district,
                        'city' => $user->city,
                        'state' => $user->state,
                        'chave_pix' => $user->chave_pix,
                        'token_paghiper' => $user->token_paghiper,
                        'key_paghiper' => $user->key_paghiper,
                        'access_token_paghiper' => $user->access_token_paghiper,
                        'access_token_mp' => $user->access_token_mp,
                        'inter_host' => $user->inter_host,
                        'inter_client_id' => $user->inter_client_id,
                        'inter_client_secret' => $user->inter_client_secret,
                        'inter_scope' => $user->inter_scope,
                        'inter_crt_file' => $user->inter_crt_file,
                        'inter_key_file' => $user->inter_key_file,
                        'inter_crt_file_webhook' => $user->inter_crt_file_webhook,
                        'inter_webhook_url_billet' => $user->inter_webhook_url_billet,
                        'inter_webhook_url_pix' => $user->inter_webhook_url_pix,
                        'inter_chave_pix' => $user->inter_chave_pix,
                        'access_token_inter' => $user->access_token_inter,
                        'use_intermedium' => $user->use_intermedium,
                        'environment_asaas' => $user->environment_asaas,
                        'at_asaas_prod' => $user->at_asaas_prod,
                        'at_asaas_test' => $user->at_asaas_test,
                        'asaas_url_test' => $user->asaas_url_test,
                        'asaas_url_prod' => $user->asaas_url_prod,
                        'api_session_whatsapp' => $user->api_session_whatsapp,
                        'api_token_whatsapp' => $user->api_token_whatsapp,
                        'api_status_whatsapp' => $user->api_status_whatsapp,
                        'day_generate_invoice' => $user->day_generate_invoice,
                        'send_generate_invoice' => $user->send_generate_invoice,
                        'typebot_id' => $user->typebot_id,
                        'typebot_enable' => $user->typebot_enable,
                        'logo' => $user->image,
                        'status' => 'Ativo',
                    ];
                    
                    if ($dryRun) {
                        $this->info("  [DRY-RUN] Criaria empresa: " . $companyData['name']);
                        $company = (object)['id' => 999, 'name' => $companyData['name']];
                    } else {
                        $company = Company::create($companyData);
                        $this->info("  ✓ Empresa criada: {$company->name}");
                    }
                }
                
                // Vincular usuário à empresa como owner
                if (!$dryRun && !$existingCompany) {
                    $company->users()->attach($user->id, ['role' => 'owner']);
                    $this->info("  ✓ Usuário vinculado como owner");
                }
                
                // Definir como empresa atual
                if (!$user->current_company_id) {
                    if ($dryRun) {
                        $this->info("  [DRY-RUN] Definiria empresa atual");
                    } else {
                        $user->update(['current_company_id' => $company->id]);
                        $this->info("  ✓ Empresa definida como atual");
                    }
                }
                
                // Contar registros a migrar
                $counts = [
                    'customers' => Customer::where('user_id', $user->id)->whereNull('company_id')->count(),
                    'services' => Service::where('user_id', $user->id)->whereNull('company_id')->count(),
                    'customer_services' => DB::table('customer_services')->where('user_id', $user->id)->whereNull('company_id')->count(),
                    'invoices' => Invoice::where('user_id', $user->id)->whereNull('company_id')->count(),
                    'invoice_notifications' => DB::table('invoice_notifications')->where('user_id', $user->id)->whereNull('company_id')->count(),
                    'payables' => Payable::where('user_id', $user->id)->whereNull('company_id')->count(),
                    'suppliers' => Supplier::where('user_id', $user->id)->whereNull('company_id')->count(),
                    'payable_categories' => PayableCategory::where('user_id', $user->id)->whereNull('company_id')->count(),
                    'email_events' => DB::table('email_events')->where('user_id', $user->id)->whereNull('company_id')->count(),
                ];
                
                $total = array_sum($counts);
                
                if ($total > 0) {
                    $this->info("  Registros a migrar: {$total}");
                    
                    foreach ($counts as $table => $count) {
                        if ($count > 0) {
                            $this->line("    - {$table}: {$count}");
                        }
                    }
                    
                    // Atualizar todos os registros do usuário com company_id
                    if (!$dryRun) {
                        Customer::where('user_id', $user->id)->whereNull('company_id')->update(['company_id' => $company->id]);
                        Service::where('user_id', $user->id)->whereNull('company_id')->update(['company_id' => $company->id]);
                        DB::table('customer_services')->where('user_id', $user->id)->whereNull('company_id')->update(['company_id' => $company->id]);
                        Invoice::where('user_id', $user->id)->whereNull('company_id')->update(['company_id' => $company->id]);
                        DB::table('invoice_notifications')->where('user_id', $user->id)->whereNull('company_id')->update(['company_id' => $company->id]);
                        Payable::where('user_id', $user->id)->whereNull('company_id')->update(['company_id' => $company->id]);
                        Supplier::where('user_id', $user->id)->whereNull('company_id')->update(['company_id' => $company->id]);
                        PayableCategory::where('user_id', $user->id)->whereNull('company_id')->update(['company_id' => $company->id]);
                        DB::table('email_events')->where('user_id', $user->id)->whereNull('company_id')->update(['company_id' => $company->id]);
                        
                        $this->info("  ✓ Registros migrados com sucesso");
                    } else {
                        $this->info("  [DRY-RUN] Migraria {$total} registros");
                    }
                } else {
                    $this->info("  ℹ Nenhum registro para migrar");
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine(2);
            
            if (!$dryRun) {
                DB::commit();
                $this->info("✓ Migração concluída com sucesso!");
            } else {
                $this->info("✓ Simulação concluída! Execute sem --dry-run para aplicar as mudanças.");
            }
            
        } catch (\Exception $e) {
            if (!$dryRun) {
                DB::rollBack();
            }
            $this->error("Erro na migração: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
