<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>{{ ($isBoleto ?? false) ? 'Boleto' : 'Cobrança' }} #{{ $invoice->id }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
            padding: 28px 30px;
        }

        /* ── HEADER ── */
        table.header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        table.header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .header-wrap {
            background: #0A0A0A;
            border-radius: 10px 10px 0 0;
            padding: 18px 22px;
        }
        .logo-cell { width: 160px; }
        .logo-cell img {
            height: 70px;
            width: auto;
            max-width: 148px;
            border-radius: 8px;
            background: #ffffff;
            padding: 6px;
        }
        .logo-fallback {
            width: 84px;
            height: 70px;
            border-radius: 8px;
            background: #ffffff;
            border: 2px solid #D4AF37;
            color: #D4AF37;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            line-height: 66px;
        }
        .company-cell { padding-left: 14px !important; }
        .company-trade {
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.01em;
        }
        .company-legal {
            font-size: 9px;
            color: #aaaaaa;
            margin-top: 3px;
        }
        .company-doc {
            font-size: 9px;
            color: #888888;
            margin-top: 1px;
        }
        .doc-num-cell {
            text-align: right;
            padding-right: 0 !important;
        }
        .doc-num-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #888;
        }
        .doc-num-value {
            font-size: 18px;
            font-weight: bold;
            color: #D4AF37;
            display: block;
            margin-top: 2px;
        }

        /* ── GOLD ACCENT BAR ── */
        .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, #D4AF37 0%, #B8962E 100%);
            margin-bottom: 0;
        }

        /* ── VALOR DESTAQUE ── */
        .amount-band {
            background: #f9f7f0;
            border-left: 3px solid #D4AF37;
            border-right: 3px solid #D4AF37;
            padding: 14px 22px;
            margin-bottom: 0;
        }
        table.amount-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.amount-table td {
            vertical-align: middle;
            padding: 0;
        }
        .amount-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #888;
        }
        .amount-value {
            font-size: 26px;
            font-weight: bold;
            color: #0A0A0A;
            letter-spacing: -0.02em;
        }
        .due-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #888;
            text-align: right;
        }
        .due-value {
            font-size: 15px;
            font-weight: bold;
            color: #0A0A0A;
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: #f0e8c0;
            color: #7a6010;
        }
        .status-badge.paid {
            background: #d4f0e0;
            color: #1a6040;
        }

        /* ── GRID DE DADOS ── */
        .section-title {
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #999;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #efefef;
        }
        .info-wrap {
            border: 1px solid #e8e8e8;
            border-top: none;
            border-radius: 0 0 0 0;
            padding: 16px 22px;
            margin-bottom: 0;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.info-table td {
            padding: 6px 12px 6px 0;
            vertical-align: top;
            border-bottom: 1px solid #f2f2f2;
        }
        table.info-table tr:last-child td {
            border-bottom: none;
        }
        .info-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #aaa;
            display: block;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 10.5px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .info-sub {
            font-size: 8.5px;
            color: #888;
            margin-top: 1px;
            display: block;
        }

        /* ── SEÇÃO DE PAGAMENTO ── */
        .payment-wrap {
            border: 1px solid #e8e8e8;
            border-top: 3px solid #D4AF37;
            border-radius: 0;
            padding: 16px 22px;
            margin-top: 14px;
        }
        .payment-title {
            font-size: 11px;
            font-weight: bold;
            color: #0A0A0A;
            margin-bottom: 12px;
        }

        /* ── LINHA DIGITÁVEL ── */
        .digitable-wrap {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .digitable-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #aaa;
            margin-bottom: 4px;
        }
        .digitable-value {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 12px;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: 0.04em;
            word-break: break-all;
        }

        /* ── BARCODE ── */
        .barcode-wrap {
            text-align: center;
            padding: 14px 0 6px;
        }
        .barcode-wrap img {
            max-width: 100%;
            height: 70px;
        }

        /* ── PIX ── */
        table.pix-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.pix-table td {
            vertical-align: top;
            padding: 0;
        }
        .pix-qr-cell { width: 160px; }
        .pix-qr-cell img {
            width: 148px;
            height: 148px;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 4px;
        }
        .pix-right { padding-left: 18px !important; }
        .pix-code-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #aaa;
            margin-bottom: 6px;
        }
        .pix-code-box {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 8.5px;
            word-break: break-all;
            background: #f9f7f0;
            border: 1px solid #e8e0c0;
            border-radius: 6px;
            padding: 10px 12px;
            line-height: 1.5;
            color: #333;
        }
        .pix-hint {
            font-size: 8.5px;
            color: #999;
            margin-top: 8px;
            line-height: 1.4;
        }

        /* ── INSTRUÇÕES ── */
        .instructions-wrap {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 0 0 6px 6px;
            padding: 10px 14px;
            margin-top: 10px;
            font-size: 8.5px;
            color: #777;
            line-height: 1.55;
        }

        /* ── PAGO ── */
        .paid-stamp {
            text-align: center;
            margin-top: 14px;
            padding: 12px 16px;
            border: 2px solid #1F7A4D;
            border-radius: 8px;
            color: #1F7A4D;
            font-weight: bold;
            font-size: 13px;
            background: #f0fbf5;
        }

        /* ── RODAPÉ ── */
        .footer-wrap {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 8px;
            color: #bbb;
        }
        .footer-wrap strong {
            color: #888;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body>

@php
    $docType  = strlen(preg_replace('/\D/', '', $company->document ?? '')) === 11 ? 'CPF' : 'CNPJ';
    $hasLogo  = !empty($logoBase64);
    $initials = collect(explode(' ', $tradeName ?: $legalName))
        ->filter()->take(2)
        ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->implode('');
    $statusClass = ($isPaid ?? false) ? 'paid' : '';
@endphp

{{-- ══════════════ HEADER ══════════════ --}}
<div class="header-wrap">
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if($hasLogo)
                    <img src="data:{{ $logoMime }};base64,{{ $logoBase64 }}" alt="{{ $tradeName ?: $legalName }}">
                @else
                    <div class="logo-fallback">{{ $initials ?: 'CS' }}</div>
                @endif
            </td>
            <td class="company-cell">
                <div class="company-trade">{{ $tradeName ?: $legalName }}</div>
                @if($tradeName && $legalName && $tradeName !== $legalName)
                    <div class="company-legal">{{ $legalName }}</div>
                @endif
                @if($documentFormatted)
                    <div class="company-doc">{{ $docType }}: {{ $documentFormatted }}</div>
                @endif
            </td>
            <td class="doc-num-cell">
                <span class="doc-num-label">Documento</span>
                <span class="doc-num-value">#{{ $invoice->id }}</span>
            </td>
        </tr>
    </table>
</div>
<div class="accent-bar"></div>

{{-- ══════════════ VALOR E VENCIMENTO ══════════════ --}}
<div class="amount-band">
    <table class="amount-table">
        <tr>
            <td>
                <div class="amount-label">Valor total</div>
                <div class="amount-value">{{ $priceFormatted }}</div>
            </td>
            <td style="text-align:right;">
                <div class="due-label">Vencimento</div>
                <div class="due-value">{{ $dateDueFormatted ?? '—' }}</div>
                <div style="margin-top:5px;">
                    <span class="status-badge {{ $statusClass }}">{{ $invoice->status }}</span>
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ══════════════ DADOS DETALHADOS ══════════════ --}}
<div class="info-wrap">
    <div class="section-title">Detalhes da cobrança</div>
    <table class="info-table">
        <tr>
            <td width="50%">
                <span class="info-label">Beneficiário</span>
                <span class="info-value">{{ $tradeName ?: $legalName }}</span>
                @if($tradeName && $legalName && $tradeName !== $legalName)
                    <span class="info-sub">{{ $legalName }}</span>
                @endif
                @if($documentFormatted)
                    <span class="info-sub">{{ $docType }}: {{ $documentFormatted }}</span>
                @endif
            </td>
            <td width="50%">
                <span class="info-label">Pagador</span>
                <span class="info-value">{{ $customerName }}</span>
                @if($customerDocument ?? false)
                    <span class="info-sub">
                        {{ strlen(preg_replace('/\D/', '', $customer->document ?? '')) === 11 ? 'CPF' : 'CNPJ' }}: {{ $customerDocument }}
                    </span>
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="info-label">Descrição / Serviço</span>
                <span class="info-value">{{ $invoice->description ?: 'Cobrança' }}</span>
            </td>
        </tr>
        @if(($isBoleto ?? false))
        <tr>
            <td colspan="2">
                <span class="info-label">Local de Pagamento</span>
                <span class="info-value" style="font-weight:normal;color:#555;">Pagável em qualquer banco ou pelo aplicativo até a data de vencimento.</span>
            </td>
        </tr>
        @endif
    </table>
</div>

{{-- ══════════════ PAGAMENTO ══════════════ --}}
@if($isPaid ?? false)

    <div class="paid-stamp">
        ✓ PAGAMENTO CONFIRMADO
        @if($datePaymentFormatted ?? false)
        — {{ $datePaymentFormatted }}
        @endif
    </div>

@elseif($isBoleto ?? false)

    <div class="payment-wrap">
        <div class="payment-title">Pague seu boleto</div>

        @if($billetLine ?? false)
            <div class="digitable-wrap">
                <div class="digitable-label">Linha digitável</div>
                <div class="digitable-value">{{ $billetLineFormatted ?: $billetLine }}</div>
            </div>
        @endif

        @if($barcodeBase64 ?? false)
            <div class="barcode-wrap">
                <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Código de barras">
            </div>
        @endif

        <div class="instructions-wrap">
            Em caso de dúvidas, entre em contato com <strong>{{ $tradeName ?: $legalName }}</strong>
            @if(!empty($company->whatsapp ?? $company->phone))
            — {{ $company->whatsapp ?? $company->phone }}
            @endif
            @if(!empty($company->email))
            — {{ $company->email }}
            @endif.
        </div>
    </div>

@elseif(($isPix ?? false) && ($pixCode ?? false))

    <div class="payment-wrap">
        <div class="payment-title">Pague com PIX</div>

        <table class="pix-table">
            <tr>
                @if($qrCodeBase64 ?? false)
                <td class="pix-qr-cell">
                    <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code PIX">
                </td>
                @endif
                <td class="pix-right">
                    <div class="pix-code-label">Código PIX — Copia e Cola</div>
                    <div class="pix-code-box">{{ $pixCode }}</div>
                    <div class="pix-hint">
                        Escaneie o QR Code ou copie o código acima no aplicativo do seu banco.
                        O pagamento PIX é processado instantaneamente.
                    </div>
                </td>
            </tr>
        </table>
    </div>

@endif

{{-- ══════════════ RODAPÉ ══════════════ --}}
<div class="footer-wrap">
    <strong>CobrançaSegura</strong> — www.cobrancasegura.com.br
    &nbsp;|&nbsp; {{ $publicUrl }}
</div>

</body>
</html>
