<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;

class StatusInterCron extends Command
{

  protected $signature = 'statusinter:cron';

  protected $description = 'Consultar Status inter';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {
    $limite_por_execucao = 50;
    $metodosAtivos = ['Boleto', 'BoletoPix'];

    $processingInvoices = Invoice::where('status', 'Processamento')
        ->whereIn('gateway_payment', ['Inter', 'Intermedium'])
        ->whereIn('payment_method', $metodosAtivos)
        ->whereNotNull('company_id')
        ->whereNotNull('transaction_id')
        ->whereHas('company', function ($query) {
            $query->where('status', 'Ativo');
        })
        ->with('company')
        ->orderBy('updated_at', 'asc')
        ->limit(10)
        ->get();

    foreach ($processingInvoices as $invoice) {
        $result = Invoice::completeInterCobrancaProcessing($invoice->id);

        if (!$result['success']) {
            \Log::warning('StatusInterCron - Conclusão cobrança - Invoice ID: '.$invoice->id.' - '.($result['message'] ?? 'Erro desconhecido'));
        }

        usleep(500000);
    }

    $invoices = Invoice::where('status', 'Pendente')
        ->whereIn('gateway_payment', ['Inter', 'Intermedium'])
        ->whereIn('payment_method', $metodosAtivos)
        ->whereNotNull('company_id')
        ->whereNotNull('transaction_id')
        ->whereHas('company', function ($query) {
            $query->where('status', 'Ativo');
        })
        ->with('company')
        ->orderBy('date_due', 'asc')
        ->limit($limite_por_execucao)
        ->get();

    $this->info('Processando '.$invoices->count().' faturas pendentes (Métodos: '.implode(', ', $metodosAtivos).')...');

    $contador = 0;
    foreach ($invoices as $invoice) {
        $contador++;

        $result = Invoice::syncInterPaymentStatus($invoice);

        if (!$result['success'] && ($result['message'] ?? '') !== 'Empresa inativa ou não encontrada.') {
            \Log::warning('StatusInterCron - Invoice ID: '.$invoice->id.' - '.($result['message'] ?? 'Erro desconhecido'));
        }

        usleep(500000);

        if ($contador % 10 == 0) {
            $this->info("Processadas {$contador} de {$invoices->count()} faturas...");
        }
    }

    $this->info('StatusInterCron finalizado - Total: '.$contador.' faturas processadas');
    return 0;
  }
}
