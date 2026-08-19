<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0A0A0A">
    <title>Cobrança — {{ $company->trade_name ?? $company->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --black: #0A0A0A;
            --black-soft: #141414;
            --gold: #D4AF37;
            --gold-dark: #B8962E;
            --gold-soft: rgba(212, 175, 55, 0.12);
            --gold-border: rgba(212, 175, 55, 0.35);
            --white: #FFFFFF;
            --off-white: #F7F6F3;
            --gray: #6B6B6B;
            --gray-light: #E8E6E1;
            --gray-muted: #9A9A9A;
            --success: #1F7A4D;
            --danger: #B33A3A;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
            --radius: 16px;
            --font: 'Outfit', sans-serif;
            --mono: 'JetBrains Mono', ui-monospace, monospace;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html { -webkit-text-size-adjust: 100%; }

        body {
            margin: 0;
            font-family: var(--font);
            background: var(--off-white);
            color: var(--black);
            line-height: 1.5;
            min-height: 100vh;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(212, 175, 55, 0.08), transparent),
                linear-gradient(180deg, #F7F6F3 0%, #F0EEE9 100%);
        }

        a { color: inherit; }

        .page {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 16px 48px;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 0;
            border-bottom: 1px solid var(--gray-light);
            margin-bottom: 28px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .brand-logo {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            object-fit: contain;
            background: var(--white);
            border: 1px solid var(--gray-light);
            flex-shrink: 0;
        }

        .brand-fallback {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: var(--black);
            color: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
            letter-spacing: 0.02em;
        }

        .brand-text {
            min-width: 0;
        }

        .brand-name {
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-legal {
            font-size: 0.72rem;
            color: var(--gray);
            margin: 2px 0 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .secure-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            color: var(--gray);
            background: var(--white);
            border: 1px solid var(--gray-light);
            padding: 8px 12px;
            border-radius: 999px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .secure-badge svg {
            width: 14px;
            height: 14px;
            color: var(--gold);
        }

        /* Greeting */
        .greeting {
            margin-bottom: 24px;
            animation: fadeUp 0.5s ease both;
        }

        .greeting h1 {
            margin: 0 0 6px;
            font-size: clamp(1.5rem, 4vw, 1.85rem);
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .greeting p {
            margin: 0;
            color: var(--gray);
            font-size: 1rem;
        }

        /* Layout grid */
        .layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            align-items: start;
        }

        @media (min-width: 860px) {
            .layout {
                grid-template-columns: 1.4fr 0.85fr;
            }

            .summary-sticky {
                position: sticky;
                top: 20px;
            }
        }

        /* Cards */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(0,0,0,0.04);
            padding: 24px;
            animation: fadeUp 0.55s ease both;
        }

        .card + .card,
        .payment-block {
            animation-delay: 0.08s;
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 20px;
            font-size: 1rem;
            font-weight: 600;
        }

        .card-title .icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--gold-soft);
            color: var(--gold-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .card-title .icon svg {
            width: 18px;
            height: 18px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 16px;
        }

        @media (max-width: 480px) {
            .detail-grid { grid-template-columns: 1fr; }
        }

        .detail-item label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gray-muted);
            margin-bottom: 4px;
            font-weight: 500;
        }

        .detail-item span,
        .detail-item strong {
            font-size: 0.95rem;
            font-weight: 500;
            word-break: break-word;
        }

        .detail-item .highlight {
            color: var(--gold-dark);
            font-weight: 700;
            font-size: 1.05rem;
        }

        /* Summary sidebar */
        .summary-card {
            background: var(--black);
            color: var(--white);
            border: none;
        }

        .summary-card .card-title {
            color: var(--gold);
            margin-bottom: 8px;
        }

        .summary-amount {
            font-size: clamp(1.75rem, 5vw, 2.15rem);
            font-weight: 700;
            letter-spacing: -0.03em;
            margin: 8px 0 20px;
            color: var(--gold);
        }

        .summary-rows {
            display: flex;
            flex-direction: column;
            gap: 14px;
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 18px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-size: 0.9rem;
        }

        .summary-row span:first-child {
            color: rgba(255,255,255,0.55);
        }

        .summary-row span:last-child {
            text-align: right;
            font-weight: 500;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            background: rgba(212, 175, 55, 0.18);
            color: var(--gold);
        }

        .status-pill.paid {
            background: rgba(31, 122, 77, 0.25);
            color: #6DDBA5;
        }

        .status-pill.overdue {
            background: rgba(179, 58, 58, 0.25);
            color: #F0A0A0;
        }

        .status-pill.cancelled {
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.7);
        }

        /* Payment section */
        .payment-block {
            margin-top: 20px;
            grid-column: 1 / -1;
        }

        .payment-intro {
            color: var(--gray);
            margin: -8px 0 22px;
            font-size: 0.95rem;
        }

        .badge-instant {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--gold-soft);
            color: var(--gold-dark);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
            margin-left: 8px;
            vertical-align: middle;
        }

        .pix-layout {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 28px;
            align-items: start;
        }

        @media (max-width: 640px) {
            .pix-layout {
                grid-template-columns: 1fr;
                justify-items: center;
                text-align: center;
            }

            .pix-copy-area { width: 100%; text-align: left; }
        }

        .qr-frame {
            background: var(--white);
            border: 2px solid var(--gold-border);
            border-radius: 14px;
            padding: 14px;
            width: 268px;
            max-width: 100%;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.1);
        }

        .qr-frame svg {
            display: block;
            width: 100%;
            height: auto;
        }

        .code-box {
            background: var(--off-white);
            border: 1px solid var(--gray-light);
            border-radius: 12px;
            padding: 14px 16px;
            font-family: var(--mono);
            font-size: 0.78rem;
            line-height: 1.55;
            word-break: break-all;
            max-height: 140px;
            overflow-y: auto;
            color: var(--black-soft);
            user-select: all;
        }

        .code-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--black);
        }

        .barcode-wrap {
            text-align: center;
            padding: 16px 8px;
            background: var(--off-white);
            border-radius: 12px;
            border: 1px solid var(--gray-light);
            margin-bottom: 20px;
            overflow-x: auto;
        }

        .barcode-wrap img {
            max-width: 100%;
            height: auto;
            min-height: 60px;
        }

        /* Buttons */
        .actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 16px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 12px;
            font-family: var(--font);
            font-size: 0.95rem;
            font-weight: 600;
            padding: 14px 20px;
            cursor: pointer;
            transition: transform 0.15s ease, background 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            width: 100%;
            min-height: 48px;
        }

        .btn:active { transform: scale(0.98); }

        .btn-primary {
            background: var(--gold);
            color: var(--black);
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.35);
        }

        .btn-primary:hover {
            background: var(--gold-dark);
        }

        .btn-primary.copied {
            background: var(--success);
            color: var(--white);
            box-shadow: none;
        }

        .btn-secondary {
            background: transparent;
            color: var(--black);
            border: 1.5px solid var(--gray-light);
        }

        .btn-secondary:hover {
            border-color: var(--gold);
            color: var(--gold-dark);
        }

        .btn svg {
            width: 18px;
            height: 18px;
        }

        /* Info / states */
        .info-banner {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            margin-top: 20px;
            padding: 14px 16px;
            background: var(--gold-soft);
            border-radius: 12px;
            border: 1px solid var(--gold-border);
            font-size: 0.88rem;
            color: var(--black-soft);
        }

        .info-banner svg {
            width: 20px;
            height: 20px;
            color: var(--gold-dark);
            flex-shrink: 0;
            margin-top: 1px;
        }

        .info-banner strong {
            display: block;
            margin-bottom: 2px;
        }

        .state-card {
            text-align: center;
            padding: 40px 24px;
            grid-column: 1 / -1;
        }

        .state-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .state-icon.success {
            background: rgba(31, 122, 77, 0.12);
            color: var(--success);
        }

        .state-icon.neutral {
            background: var(--gold-soft);
            color: var(--gold-dark);
        }

        .state-icon svg { width: 32px; height: 32px; }

        .state-card h2 {
            margin: 0 0 8px;
            font-size: 1.35rem;
        }

        .state-card p {
            margin: 0;
            color: var(--gray);
        }

        .overdue-banner {
            background: rgba(179, 58, 58, 0.08);
            border: 1px solid rgba(179, 58, 58, 0.25);
            color: var(--danger);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.88rem;
            margin-bottom: 20px;
            font-weight: 500;
            grid-column: 1 / -1;
        }

        /* Help + share + security */
        .help-card .help-contacts {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 12px;
        }

        .help-contacts a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--black-soft);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .help-contacts a:hover { color: var(--gold-dark); }

        .help-contacts svg {
            width: 18px;
            height: 18px;
            color: var(--gold);
        }

        .share-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .share-row .btn {
            width: auto;
            flex: 1 1 140px;
            padding: 12px 16px;
            min-height: 44px;
            font-size: 0.88rem;
        }

        .security-note {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 16px 0 8px;
            color: var(--gray);
            font-size: 0.85rem;
            grid-column: 1 / -1;
        }

        .security-note svg {
            width: 18px;
            height: 18px;
            color: var(--gold);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .security-note strong {
            display: block;
            color: var(--black-soft);
            font-size: 0.9rem;
            margin-bottom: 2px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid var(--gray-light);
            text-align: center;
            color: var(--gray-muted);
            font-size: 0.82rem;
        }

        .footer .platform {
            font-weight: 600;
            color: var(--gray);
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }

        .footer a {
            color: var(--gray);
            text-decoration: none;
        }

        .footer a:hover { color: var(--gold-dark); }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>
<body>
@php
    $initials = collect(explode(' ', $companyName))
        ->filter()
        ->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->implode('');
    $firstName = $customerFirstName ?? explode(' ', trim($customer->name ?? 'Cliente'))[0];
@endphp

<div class="page">
    <header class="header">
        <div class="brand">
            @if($logoUrl)
                <img class="brand-logo" src="{{ $logoUrl }}" alt="{{ $companyName }}" width="56" height="56">
            @else
                <div class="brand-fallback" aria-hidden="true">{{ $initials ?: 'CS' }}</div>
            @endif
            <div class="brand-text">
                {{-- Nome fantasia em destaque, razão social abaixo se diferente --}}
                <p class="brand-name">{{ $tradeName ?: $legalName }}</p>
                @if($tradeName && $legalName && $tradeName !== $legalName)
                    <p class="brand-legal">{{ $legalName }}</p>
                @endif
            </div>
        </div>
        <div class="secure-badge" title="Conexão segura">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Ambiente seguro
        </div>
    </header>

    <section class="greeting">
        <h1>Olá, {{ $firstName }}!</h1>
        <p>
            @if($isPaid)
                Esta cobrança já foi paga.
            @elseif($isCancelled)
                Esta cobrança não está disponível para pagamento.
            @else
                Confira abaixo os detalhes da sua cobrança.
            @endif
        </p>
    </section>

    @if($isOverdue && !$isPaid && !$isCancelled)
        <div class="overdue-banner">
            Esta cobrança está vencida desde {{ $dateDueFormatted }}. Você ainda pode realizar o pagamento abaixo.
        </div>
    @endif

    <div class="layout">
        <div class="card">
            <h2 class="card-title">
                <span class="icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/>
                    </svg>
                </span>
                Detalhes da cobrança
            </h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Descrição</label>
                    <strong>{{ $invoice->description ?: 'Cobrança' }}</strong>
                </div>
                <div class="detail-item">
                    <label>Vencimento</label>
                    <span class="{{ $isOverdue && !$isPaid ? 'highlight' : '' }}">{{ $dateDueFormatted ?? '—' }}</span>
                </div>
                <div class="detail-item">
                    <label>Valor</label>
                    <span class="highlight">{{ $priceFormatted }}</span>
                </div>
                <div class="detail-item">
                    <label>Status</label>
                    <strong>{{ $invoice->status }}</strong>
                </div>
                <div class="detail-item">
                    <label>Beneficiário</label>
                    <strong>{{ $tradeName ?: $legalName }}</strong>
                    @if($tradeName && $legalName && $tradeName !== $legalName)
                        <span style="display:block;font-size:0.8rem;color:var(--gray);margin-top:2px;">{{ $legalName }}</span>
                    @endif
                </div>
                @if($documentFormatted)
                <div class="detail-item">
                    <label>{{ strlen(preg_replace('/\D/', '', $company->document ?? '')) === 11 ? 'CPF' : 'CNPJ' }}</label>
                    <strong>{{ $documentFormatted }}</strong>
                </div>
                @endif
                @if($invoice->id)
                <div class="detail-item">
                    <label>Documento</label>
                    <span>#{{ $invoice->id }}</span>
                </div>
                @endif
            </div>
        </div>

        <aside class="summary-sticky">
            <div class="card summary-card">
                <h2 class="card-title">Resumo</h2>
                <div class="summary-amount">{{ $priceFormatted }}</div>
                <div class="summary-rows">
                    <div class="summary-row">
                        <span>Vencimento</span>
                        <span>{{ $dateDueFormatted ?? '—' }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Beneficiário</span>
                        <span>{{ $companyName }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Status</span>
                        <span>
                            @if($isPaid)
                                <span class="status-pill paid">Pago</span>
                            @elseif($isCancelled)
                                <span class="status-pill cancelled">{{ $invoice->status }}</span>
                            @elseif($isOverdue)
                                <span class="status-pill overdue">Vencida</span>
                            @else
                                <span class="status-pill">Pendente</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </aside>

        @if($isPaid)
            <div class="card state-card">
                <div class="state-icon success" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                </div>
                <h2>Pagamento confirmado</h2>
                <p>
                    Esta cobrança já foi paga.
                    @if($datePaymentFormatted)
                        Data do pagamento: <strong>{{ $datePaymentFormatted }}</strong>.
                    @endif
                </p>
            </div>
        @elseif($isCancelled)
            <div class="card state-card">
                <div class="state-icon neutral" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>
                    </svg>
                </div>
                <h2>Cobrança indisponível</h2>
                <p>Esta cobrança está com status <strong>{{ $invoice->status }}</strong> e não pode ser paga por esta página.</p>
            </div>
        @elseif($isPix && $pixCode)
            <section class="card payment-block">
                <h2 class="card-title">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.5 3.5l3.2 3.2a1.5 1.5 0 010 2.1L11.3 13l-2.1-2.1 4.4-4.4a.5.5 0 000-.7L10.4 2.6a.5.5 0 00-.7 0L3.5 8.8a1.5 1.5 0 000 2.1l3.2 3.2L3.5 17.3a1.5 1.5 0 000 2.1l3.2 3.2a1.5 1.5 0 002.1 0l3.2-3.2 3.2 3.2a1.5 1.5 0 002.1 0l6.2-6.2a1.5 1.5 0 000-2.1l-3.2-3.2 3.2-3.2a1.5 1.5 0 000-2.1L17.8 3.5a1.5 1.5 0 00-2.1 0L12.5 6.7 9.3 3.5a1.5 1.5 0 00-2.1 0L5.5 5.2l7-1.7z" opacity=".9"/>
                        </svg>
                    </span>
                    Pague com PIX
                    <span class="badge-instant">Aprovação rápida</span>
                </h2>
                <p class="payment-intro">Escaneie o QR Code com o aplicativo do seu banco ou copie o código PIX para realizar o pagamento.</p>

                <div class="pix-layout">
                    <div class="qr-frame" aria-label="QR Code PIX">
                        {!! $qrCodeSvg !!}
                    </div>
                    <div class="pix-copy-area">
                        <span class="code-label">Código PIX — Copia e Cola</span>
                        <div class="code-box" id="pix-code">{{ $pixCode }}</div>
                <div class="actions">
                    <button type="button" class="btn btn-primary" id="btn-copy-pix" data-copy="pix-code">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                        <span class="btn-label">Copiar código PIX</span>
                    </button>
                    <a class="btn btn-secondary" href="{{ route('public.invoice.print', $invoice->public_token) }}" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/></svg>
                        Imprimir cobrança
                    </a>
                </div>
                    </div>
                </div>

                <div class="info-banner">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    <div>
                        <strong>Pagamento instantâneo</strong>
                        O pagamento via PIX pode ser identificado rapidamente. Após realizar o pagamento, aguarde a confirmação da cobrança.
                    </div>
                </div>
            </section>
        @elseif($isBoleto)
            <section class="card payment-block">
                <h2 class="card-title">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 6h16M4 10h16M4 14h10M4 18h16"/>
                        </svg>
                    </span>
                    Pague seu boleto
                </h2>
                <p class="payment-intro">Utilize o código de barras ou a linha digitável no aplicativo do seu banco.</p>

                @if($barcode)
                <div class="barcode-wrap">
                    <img
                        src="{{ route('public.invoice.barcode', $invoice->public_token) }}"
                        alt="Código de barras do boleto"
                        width="480"
                        height="80"
                    >
                </div>
                @endif

                @if($billetLine)
                <span class="code-label">Linha digitável</span>
                <div class="code-box" id="billet-code">{{ $billetLineFormatted ?: $billetLine }}</div>
                <div class="actions">
                    <button type="button" class="btn btn-primary" id="btn-copy-billet" data-copy-raw="{{ preg_replace('/\D/', '', $billetLine) }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                        <span class="btn-label">Copiar linha digitável</span>
                    </button>
                    <a class="btn btn-secondary" href="{{ route('public.invoice.print', $invoice->public_token) }}" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/></svg>
                        Imprimir cobrança
                    </a>
                </div>
                @else
                <div class="info-banner">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                    <div>
                        <strong>Dados do boleto indisponíveis</strong>
                        Os dados de pagamento desta cobrança ainda não estão disponíveis. Entre em contato com o beneficiário.
                    </div>
                </div>
                @endif
            </section>
        @else
            <div class="card state-card payment-block">
                <div class="state-icon neutral" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
                    </svg>
                </div>
                <h2>Pagamento não disponível nesta página</h2>
                <p>Esta cobrança utiliza a forma de pagamento <strong>{{ $invoice->payment_method }}</strong>. Entre em contato com o beneficiário para mais informações.</p>
            </div>
        @endif

        <div class="card help-card" style="grid-column: 1 / -1;">
            <h2 class="card-title">
                <span class="icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/>
                    </svg>
                </span>
                Precisa de ajuda?
            </h2>
            <p style="margin:0;color:var(--gray);font-size:0.95rem;">Em caso de dúvidas sobre esta cobrança, entre em contato com o beneficiário.</p>
            <div class="help-contacts">
                @if(!empty($company->whatsapp) || !empty($company->phone))
                <a href="https://wa.me/55{{ preg_replace('/\D/', '', $company->whatsapp ?: $company->phone) }}" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                    {{ formatPhone(preg_replace('/\D/', '', $company->whatsapp ?: $company->phone)) }}
                </a>
                @endif
                @if(!empty($company->email))
                <a href="mailto:{{ $company->email }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>
                    {{ $company->email }}
                </a>
                @endif
            </div>

            <div class="share-row">
                <a class="btn btn-secondary" href="{{ route('public.invoice.print', $invoice->public_token) }}" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/></svg>
                    Imprimir cobrança
                </a>
                <button type="button" class="btn btn-secondary" id="btn-share">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.59 13.51l6.83 3.98M15.41 6.51l-6.82 3.98"/></svg>
                    <span class="btn-label">Compartilhar cobrança</span>
                </button>
                <a class="btn btn-secondary" id="btn-whatsapp-share" href="https://wa.me/?text={{ urlencode('Segue o link da cobrança: '.$publicUrl) }}" target="_blank" rel="noopener">
                    WhatsApp
                </a>
            </div>
        </div>

        <div class="security-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <div>
                <strong>Ambiente seguro</strong>
                Seus dados estão protegidos. Esta página utiliza conexão segura para proteger as informações da sua cobrança.
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="platform">CobrançaSegura</div>
        <a href="https://www.cobrancasegura.com.br" target="_blank" rel="noopener">www.cobrancasegura.com.br</a>
    </footer>
</div>

<script>
(function () {
    var shareUrl = @json($publicUrl);
    var shareTitle = @json('Cobrança — ' . ($company->trade_name ?: $company->name));

    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) {}
        document.body.removeChild(ta);
    }

    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
        fallbackCopy(text);
        return Promise.resolve();
    }

    function flashButton(btn, okLabel) {
        var label = btn.querySelector('.btn-label');
        var original = label ? label.textContent : btn.textContent;
        btn.classList.add('copied');
        if (label) label.textContent = okLabel;
        else btn.textContent = okLabel;
        setTimeout(function () {
            btn.classList.remove('copied');
            if (label) label.textContent = original;
            else btn.textContent = original;
        }, 2500);
    }

    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var el = document.getElementById(btn.getAttribute('data-copy'));
            if (!el) return;
            copyText(el.textContent.trim()).then(function () {
                flashButton(btn, '✓ Código copiado');
            });
        });
    });

    document.querySelectorAll('[data-copy-raw]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            copyText(btn.getAttribute('data-copy-raw')).then(function () {
                flashButton(btn, '✓ Linha digitável copiada');
            });
        });
    });

    var shareBtn = document.getElementById('btn-share');
    if (shareBtn) {
        shareBtn.addEventListener('click', function () {
            if (navigator.share) {
                navigator.share({ title: shareTitle, url: shareUrl, text: 'Segue o link da cobrança' })
                    .catch(function () {});
                return;
            }
            copyText(shareUrl).then(function () {
                flashButton(shareBtn, '✓ Link copiado');
            });
        });
    }
})();
</script>
</body>
</html>
