<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\InvoiceNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateInterInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 180;

    protected int $invoiceId;
    protected bool $sendEmail;
    protected bool $sendWhatsapp;

    public function __construct(int $invoiceId, bool $sendEmail = false, bool $sendWhatsapp = false)
    {
        $this->invoiceId = $invoiceId;
        $this->sendEmail = $sendEmail;
        $this->sendWhatsapp = $sendWhatsapp;
    }

    public function handle(): void
    {
        $invoice = Invoice::with('company')->find($this->invoiceId);

        if (!$invoice) {
            Log::warning('GenerateInterInvoiceJob: fatura não encontrada', ['invoice_id' => $this->invoiceId]);
            return;
        }

        if (!in_array($invoice->status, ['Gerando', 'Erro', 'Processamento'], true)) {
            Log::info('GenerateInterInvoiceJob: status não elegível, ignorando', [
                'invoice_id' => $invoice->id,
                'status' => $invoice->status,
            ]);
            return;
        }

        if ($invoice->status === 'Processamento' && !empty($invoice->transaction_id)) {
            $complete = Invoice::completeInterCobrancaProcessing($invoice->id);
            if (!($complete['completed'] ?? false)) {
                return;
            }
            $this->notifyIfNeeded($invoice->id);
            return;
        }

        $method = $invoice->payment_method;
        $result = null;

        if ($method === 'Boleto') {
            $result = Invoice::generateBilletIntermedium($invoice->id);
        } elseif ($method === 'BoletoPix') {
            $result = Invoice::generateBilletPixIntermedium($invoice->id);
        } elseif ($method === 'Pix') {
            $result = Invoice::generatePixIntermedium($invoice->id);
            if (($result['status'] ?? '') === 'success' && empty($result['invoice_updated'])) {
                Invoice::where('id', $invoice->id)->update(['status' => 'Pendente']);
            }
        } else {
            Log::warning('GenerateInterInvoiceJob: método não suportado', [
                'invoice_id' => $invoice->id,
                'payment_method' => $method,
            ]);
            return;
        }

        if (($result['status'] ?? '') === 'reject') {
            Invoice::where('id', $invoice->id)->update([
                'status' => 'Erro',
                'msg_erro' => json_encode($result['message'] ?? $result),
            ]);
            Log::error('GenerateInterInvoiceJob: falha na geração', [
                'invoice_id' => $invoice->id,
                'result' => $result,
            ]);
            return;
        }

        $invoice->refresh();
        if (in_array($invoice->status, ['Pendente', 'Processamento'], true)) {
            if ($invoice->status === 'Pendente') {
                $this->notifyIfNeeded($invoice->id);
            }
        } elseif (empty($result['invoice_updated'])) {
            Invoice::where('id', $invoice->id)->update([
                'status' => $result['invoice_status'] ?? 'Pendente',
            ]);
            if (($result['invoice_status'] ?? 'Pendente') === 'Pendente') {
                $this->notifyIfNeeded($invoice->id);
            }
        }
    }

    private function notifyIfNeeded(int $invoiceId): void
    {
        if ($this->sendEmail) {
            InvoiceNotification::Email($invoiceId);
        }

        if ($this->sendWhatsapp && date('l') !== 'Sunday') {
            $now = Carbon::now();
            $start = Carbon::createFromTimeString('08:00');
            $end = Carbon::createFromTimeString('19:00');
            if ($now->between($start, $end)) {
                InvoiceNotification::Whatsapp($invoiceId);
            }
        }
    }
}
