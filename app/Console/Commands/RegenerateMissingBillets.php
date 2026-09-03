<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class RegenerateMissingBillets extends Command
{
    protected $signature = 'invoices:regenerate-missing
                            {--company= : ID da empresa (opcional; processa todas se omitido)}
                            {--since= : Data mínima de emissão (YYYY-MM-DD)}
                            {--dry-run : Exibe o que seria processado sem executar}
                            {--local-only : Corrige Asaas com barCode (44 dígitos) sem chamar a API}
                            {--limit=50 : Máximo de faturas a processar por execução}';

    protected $description = 'Busca linha digitável ausente ou corrige boletos Asaas salvos com código de barras (44 dígitos)';

    public function handle(): int
    {
        $companyId = $this->option('company');
        $since     = $this->option('since');
        $dryRun    = $this->option('dry-run');
        $localOnly = $this->option('local-only');
        $limit     = (int) $this->option('limit');

        $query = Invoice::query()
            ->whereNotNull('transaction_id')
            ->where('transaction_id', '!=', '')
            ->whereIn('status', ['Pendente', 'Gerando', 'Processamento'])
            ->whereIn('payment_method', ['Boleto', 'BoletoPix'])
            ->where(function ($q) {
                $q->whereNull('billet_digitable')
                    ->orWhere('billet_digitable', '')
                    ->orWhere(function ($asaasQuery) {
                        $asaasQuery->where('gateway_payment', 'Asaas')
                            ->whereRaw('CHAR_LENGTH(billet_digitable) = 44')
                            ->whereRaw("billet_digitable REGEXP '^[0-9]+$'");
                    });
            })
            ->orderBy('id')
            ->limit($limit);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($since) {
            $query->whereDate('date_invoice', '>=', $since);
        }

        $invoices = $query->get([
            'id',
            'company_id',
            'gateway_payment',
            'payment_method',
            'status',
            'transaction_id',
            'date_invoice',
            'billet_digitable',
        ]);

        if ($invoices->isEmpty()) {
            $this->warn('Nenhuma fatura encontrada.');
            $this->line('Critérios: boleto pendente com transaction_id e billet_digitable vazio OU Asaas com 44 dígitos numéricos.');
            return self::SUCCESS;
        }

        $this->info("Encontradas {$invoices->count()} fatura(s)." . ($dryRun ? ' [DRY-RUN]' : ''));
        $this->newLine();

        $this->table(
            ['ID', 'Emissão', 'Empresa', 'Gateway', 'Método', 'Dígitos', 'Transaction ID'],
            $invoices->map(fn ($invoice) => [
                $invoice->id,
                $invoice->date_invoice,
                $invoice->company_id,
                $invoice->gateway_payment,
                $invoice->payment_method,
                strlen(preg_replace('/\D/', '', (string) $invoice->billet_digitable)),
                $invoice->transaction_id,
            ])->toArray()
        );

        if ($dryRun) {
            $this->warn('Modo dry-run: nenhuma ação executada.');
            return self::SUCCESS;
        }

        if (! $this->option('no-interaction') && ! $this->confirm("Confirma o processamento de {$invoices->count()} fatura(s)?", true)) {
            $this->info('Operação cancelada.');
            return self::SUCCESS;
        }

        $this->newLine();
        $success = 0;
        $errors  = 0;

        foreach ($invoices as $invoice) {
            $digits = strlen(preg_replace('/\D/', '', (string) $invoice->billet_digitable));
            $label = '#' . str_pad($invoice->id, 6) . " [{$invoice->gateway_payment}/{$invoice->payment_method}/{$digits}d]";
            $this->output->write("  $label ... ");

            try {
                $result = $this->fetchBilletDigitable($invoice, $localOnly);

                if ($result['success']) {
                    $detail = $result['completed'] ? ($result['message'] ?? 'linha digitável salva') : 'ainda processando no gateway';
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

            usleep(400_000);
        }

        $this->newLine();
        $this->info("Concluído: {$success} sucesso(s), {$errors} erro(s).");

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function fetchBilletDigitable(Invoice $invoice, bool $localOnly = false): array
    {
        $gw = $invoice->gateway_payment;
        $digits = strlen(preg_replace('/\D/', '', (string) $invoice->billet_digitable));

        if ($gw === 'Asaas' && ($localOnly || $digits === 44)) {
            if ($localOnly) {
                $localResult = Invoice::fixAsaasBarcodeStoredAsDigitable($invoice->id);

                return [
                    'success'   => $localResult['success'] ?? false,
                    'completed' => $localResult['success'] ?? false,
                    'message'   => $localResult['message'] ?? 'Erro desconhecido',
                ];
            }

            $apiResult = Invoice::refreshAsaasBilletDigitable($invoice->id);
            if ($apiResult['success']) {
                return [
                    'success'   => true,
                    'completed' => true,
                    'message'   => $apiResult['message'] ?? 'Linha digitável atualizada via API',
                ];
            }

            $localResult = Invoice::fixAsaasBarcodeStoredAsDigitable($invoice->id);

            return [
                'success'   => $localResult['success'] ?? false,
                'completed' => $localResult['success'] ?? false,
                'message'   => ($localResult['success'] ?? false)
                    ? 'API falhou; corrigido localmente'
                    : ($apiResult['message'] ?? $localResult['message'] ?? 'Erro desconhecido'),
            ];
        }

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
