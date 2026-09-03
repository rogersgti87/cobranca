<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class RegenerateMissingBillets extends Command
{
    protected $signature = 'invoices:regenerate-missing
                            {--company=  : ID da empresa (opcional; processa todas se omitido)}
                            {--dry-run   : Exibe o que seria processado sem executar}
                            {--limit=50  : Máximo de faturas a processar por execução}';

    protected $description = 'Busca e salva a linha digitável/PIX de faturas que já existem no gateway mas estão sem código';

    public function handle(): int
    {
        $companyId = $this->option('company');
        $dryRun    = $this->option('dry-run');
        $limit     = (int) $this->option('limit');

        // Apenas faturas que JÁ foram criadas no gateway (transaction_id preenchido)
        // mas cujo billet_digitable está vazio ou nulo.
        $query = Invoice::query()
            ->whereNotNull('transaction_id')
            ->where('transaction_id', '!=', '')
            ->whereIn('status', ['Pendente', 'Gerando', 'Processamento'])
            ->whereIn('payment_method', ['Boleto', 'BoletoPix'])
            ->where(function ($q) {
                $q->whereNull('billet_digitable')
                  ->orWhere('billet_digitable', '');
            })
            ->orderBy('id')
            ->limit($limit);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $invoices = $query->get(['id', 'company_id', 'gateway_payment', 'payment_method', 'status', 'transaction_id']);

        if ($invoices->isEmpty()) {
            $this->info('Nenhuma fatura encontrada.');
            return self::SUCCESS;
        }

        $this->info("Encontradas {$invoices->count()} fatura(s)." . ($dryRun ? ' [DRY-RUN]' : ''));
        $this->newLine();

        $this->table(
            ['ID', 'Empresa', 'Gateway', 'Método', 'Status', 'Transaction ID'],
            $invoices->map(fn($i) => [
                $i->id,
                $i->company_id,
                $i->gateway_payment,
                $i->payment_method,
                $i->status,
                $i->transaction_id,
            ])->toArray()
        );

        if ($dryRun) {
            $this->warn('Modo dry-run: nenhuma ação executada.');
            return self::SUCCESS;
        }

        if (! $this->confirm("Confirma a busca da linha digitável para as {$invoices->count()} fatura(s)?", true)) {
            $this->info('Operação cancelada.');
            return self::SUCCESS;
        }

        $this->newLine();
        $success = 0;
        $errors  = 0;

        foreach ($invoices as $invoice) {
            $label = '#' . str_pad($invoice->id, 6) . " [{$invoice->gateway_payment}/{$invoice->payment_method}]";
            $this->output->write("  $label ... ");

            try {
                $result = $this->fetchBilletDigitable($invoice);

                if ($result['success']) {
                    $detail = $result['completed'] ? 'linha digitável salva' : 'ainda processando no gateway';
                    $this->line("<fg=green>OK</> ($detail)");
                    $success++;
                } else {
                    $this->error('ERRO: ' . $result['message']);
                    $errors++;
                }
            } catch (\Throwable $e) {
                $this->error('EXCEÇÃO: ' . $e->getMessage());
                $errors++;
            }

            usleep(400_000); // 400ms para não sobrecarregar a API do gateway
        }

        $this->newLine();
        $this->info("Concluído: {$success} sucesso(s), {$errors} erro(s).");

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Consulta o gateway pelo transaction_id existente e persiste o billet_digitable.
     * Nunca cria uma nova cobrança.
     */
    private function fetchBilletDigitable(Invoice $invoice): array
    {
        $gw = $invoice->gateway_payment;

        if ($gw === 'Intermedium') {
            $result = Invoice::completeInterCobrancaProcessing($invoice->id);
            return [
                'success'   => $result['success'] ?? false,
                'completed' => $result['completed'] ?? false,
                'message'   => $result['message'] ?? 'Erro desconhecido',
            ];
        }

        if ($gw === 'Asaas') {
            $result = Invoice::refreshAsaasBilletDigitable($invoice->id);

            return [
                'success'   => $result['success'] ?? false,
                'completed' => $result['success'] ?? false,
                'message'   => $result['message'] ?? 'Erro desconhecido',
            ];
        }

        return [
            'success'   => false,
            'completed' => false,
            'message'   => "Gateway '{$gw}' não suportado por este comando.",
        ];
    }
}
