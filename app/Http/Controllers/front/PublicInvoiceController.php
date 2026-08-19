<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Helpers\BoletoHelper;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\Renderers\PngRenderer;
use Picqer\Barcode\Types\TypeInterleaved25;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PublicInvoiceController extends Controller
{
    /**
     * Página pública de pagamento da cobrança.
     */
    public function show(string $token)
    {
        return view('front.payment.show', $this->buildPaymentData($token));
    }

    /**
     * Gera e abre o PDF da cobrança (modelo público).
     */
    public function print(string $token)
    {
        $data = $this->buildPaymentData($token, true);

        $pdf = Pdf::loadView('front.payment.print', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'cobranca-' . $data['invoice']->id . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Gera dinamicamente a imagem do código de barras (ITF).
     */
    public function barcode(string $token)
    {
        $invoice = $this->findByToken($token);

        if (!in_array($invoice->payment_method, ['Boleto', 'BoletoPix'], true)) {
            abort(404);
        }

        $barcode = BoletoHelper::digitableToBarcode($invoice->billet_digitable);

        if (!$barcode) {
            abort(404);
        }

        $barcodeObj = (new TypeInterleaved25())->getBarcode($barcode);
        $renderer = new PngRenderer();
        $png = $renderer->render($barcodeObj, $barcodeObj->getWidth() * 2, 80);

        return response($png)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'private, max-age=300');
    }

    private function buildPaymentData(string $token, bool $forPdf = false): array
    {
        $invoice = $this->findByToken($token);

        $company = $invoice->company;
        $customer = $invoice->customer ?? $invoice->customerService?->customer;

        $isPaid = $invoice->status === 'Pago';
        $isCancelled = in_array($invoice->status, ['Cancelado', 'Erro'], true);
        $isOverdue = !$isPaid
            && !$isCancelled
            && $invoice->date_due
            && $invoice->date_due < now()->toDateString();

        $paymentMethod = $invoice->payment_method;
        $isPix = $paymentMethod === 'Pix';
        $isBoleto = in_array($paymentMethod, ['Boleto', 'BoletoPix'], true);

        $pixCode = $invoice->pix_digitable;
        $qrCodeSvg = null;
        $qrCodeBase64 = null;

        if ($isPix && $pixCode && !$isPaid && !$isCancelled) {
            if ($forPdf) {
                $qrCodeBase64 = base64_encode(
                    QrCode::format('png')->size(200)->margin(1)->generate($pixCode)
                );
            } else {
                $qrCodeSvg = QrCode::format('svg')
                    ->size(240)
                    ->margin(1)
                    ->errorCorrection('M')
                    ->generate($pixCode);
            }
        }

        $billetLine = $invoice->billet_digitable;
        $barcode = $isBoleto ? BoletoHelper::digitableToBarcode($billetLine) : null;
        $billetLineFormatted = $isBoleto ? BoletoHelper::formatDigitableLine($billetLine) : null;
        $barcodeBase64 = null;

        if ($forPdf && $barcode) {
            $barcodeObj = (new TypeInterleaved25())->getBarcode($barcode);
            $renderer = new PngRenderer();
            $barcodeBase64 = base64_encode(
                $renderer->render($barcodeObj, $barcodeObj->getWidth() * 2, 60)
            );
        }

        // Nome fantasia (trade_name) como nome principal de exibição; name como razão social
        $tradeName   = trim($company->trade_name ?? '');
        $legalName   = trim($company->name ?? '');
        $companyName = $tradeName ?: $legalName; // para compatibilidade com views

        $customerName = $customer->name ?? 'Cliente';
        $customerFirstName = explode(' ', trim($customerName))[0];
        $customerDocument = $this->formatDocument($customer->document ?? null);

        // Logo em base64 para o PDF (DomPDF não acessa URLs externas)
        $logoBase64 = null;
        $logoMime   = 'image/png';
        if ($forPdf && $company && !empty($company->logo)) {
            $logoPath = $company->logo;
            // Normaliza caminhos como "/storage/photos/..." para o disco público
            $diskRelative = ltrim(preg_replace('#^/storage/#', '', $logoPath), '/');
            $diskAbsolute = Storage::disk('public')->path($diskRelative);
            if (file_exists($diskAbsolute)) {
                $logoBase64 = base64_encode(file_get_contents($diskAbsolute));
                $logoMime   = mime_content_type($diskAbsolute) ?: 'image/png';
            }
        }

        $canPay = !$isPaid && !$isCancelled && (($isPix && $pixCode) || ($isBoleto && $billetLine));

        return [
            'invoice'       => $invoice,
            'company'       => $company,
            'customer'      => $customer,
            'tradeName'     => $tradeName,
            'legalName'     => $legalName,
            'companyName'   => $companyName,
            'customerName'      => $customerName,
            'customerFirstName' => $customerFirstName,
            'customerDocument'  => $customerDocument,
            'logoUrl'           => $this->resolveLogoUrl($company),
            'logoBase64'        => $logoBase64,
            'logoMime'          => $logoMime,
            'documentFormatted' => $this->formatDocument($company->document ?? null),
            'isPaid' => $isPaid,
            'isCancelled' => $isCancelled,
            'isOverdue' => $isOverdue,
            'isPix' => $isPix,
            'isBoleto' => $isBoleto,
            'canPay' => $canPay,
            'pixCode' => $pixCode,
            'qrCodeSvg' => $qrCodeSvg,
            'qrCodeBase64' => $qrCodeBase64,
            'billetLine' => $billetLine,
            'billetLineFormatted' => $billetLineFormatted,
            'barcode' => $barcode,
            'barcodeBase64' => $barcodeBase64,
            'publicUrl' => $invoice->publicUrl(),
            'priceFormatted' => 'R$ ' . number_format((float) $invoice->price, 2, ',', '.'),
            'dateDueFormatted' => $invoice->date_due ? date('d/m/Y', strtotime($invoice->date_due)) : null,
            'datePaymentFormatted' => $invoice->date_payment ? date('d/m/Y', strtotime($invoice->date_payment)) : null,
        ];
    }

    private function findByToken(string $token): Invoice
    {
        return Invoice::with(['company', 'customer', 'customerService.customer'])
            ->where('public_token', $token)
            ->firstOrFail();
    }

    private function resolveLogoUrl($company): ?string
    {
        if (!$company || empty($company->logo)) {
            return null;
        }

        $logo = $company->logo;

        if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
            return $logo;
        }

        if (Storage::disk('public')->exists($logo)) {
            return asset('storage/' . ltrim($logo, '/'));
        }

        return asset(ltrim($logo, '/'));
    }

    private function formatDocument(?string $document): ?string
    {
        if (!$document) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $document);

        if (strlen($digits) === 11) {
            return Mask('###.###.###-##', $digits);
        }

        if (strlen($digits) === 14) {
            return Mask('##.###.###/####-##', $digits);
        }

        return $document;
    }
}
