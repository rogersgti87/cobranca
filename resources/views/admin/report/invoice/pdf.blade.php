<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Contas a Receber</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1F2937;
            line-height: 1.4;
        }
        
        .header {
            background: linear-gradient(135deg, #FFBD59 0%, #FFA500 100%);
            color: #1F2937;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 11px;
            opacity: 0.9;
        }
        
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #F5F5DC;
            border-radius: 6px;
            border: 1px solid rgba(0,0,0,0.1);
        }
        
        .info-section h2 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1F2937;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 10px;
        }
        
        .totals-grid {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 10px;
        }
        
        .total-card {
            background-color: #FFFFFF;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            vertical-align: top;
        }
        
        .total-card .label {
            font-size: 9px;
            color: #6B7280;
            margin-bottom: 5px;
        }
        
        .total-card .value {
            font-size: 16px;
            font-weight: bold;
            color: #1F2937;
        }
        
        .total-card.pendente .value {
            color: #FFBD59;
        }
        
        .total-card.pago .value {
            color: #22C55E;
        }
        
        .total-card.cancelado .value {
            color: #F87171;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #FFFFFF;
        }
        
        .table thead {
            background-color: #1F2937;
            color: #FFFFFF;
        }
        
        .table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #374151;
        }
        
        .table td {
            padding: 8px;
            font-size: 9px;
            border: 1px solid #E5E7EB;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        
        .table tbody tr:hover {
            background-color: #F3F4F6;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }
        
        .status-pendente {
            background-color: #FFBD5920;
            color: #FFBD59;
        }
        
        .status-pago {
            background-color: #22C55E20;
            color: #22C55E;
        }
        
        .status-cancelado {
            background-color: #F8717120;
            color: #F87171;
        }
        
        .status-expirado {
            background-color: #F59E0B20;
            color: #F59E0B;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            font-size: 9px;
            color: #6B7280;
            text-align: center;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            .header {
                page-break-after: avoid;
            }
            
            .table thead {
                display: table-header-group;
            }
            
            .table tbody tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Contas a Receber</h1>
        <p>Gerado em: {{ date('d/m/Y H:i:s') }} | Usuário: {{ $user->name ?? 'N/A' }}</p>
    </div>

    <div class="info-section">
        <h2>Filtros Aplicados</h2>
        <div class="info-row">
            <span><strong>Tipo de Data:</strong> {{ $filters['date_type'] == 'date_due' ? 'Data de Vencimento' : ($filters['date_type'] == 'date_payment' ? 'Data de Recebimento' : 'Data da Fatura') }}</span>
        </div>
        @if($filters['date_ini'] && $filters['date_end'])
        <div class="info-row">
            <span><strong>Período:</strong> {{ date('d/m/Y', strtotime($filters['date_ini'])) }} até {{ date('d/m/Y', strtotime($filters['date_end'])) }}</span>
        </div>
        @endif
        @if(!empty($filters['status']))
        <div class="info-row">
            <span><strong>Status:</strong> {{ implode(', ', $filters['status']) }}</span>
        </div>
        @endif
        @if(!empty($filters['payment_method']))
        <div class="info-row">
            <span><strong>Forma de Pagamento:</strong> {{ implode(', ', $filters['payment_method']) }}</span>
        </div>
        @endif
    </div>

    <table class="totals-grid" style="width: 100%; border-collapse: separate; border-spacing: 10px;">
        <tr>
            <td class="total-card" style="width: 25%;">
                <div class="label">Total</div>
                <div class="value">R$ {{ number_format($totals['total'], 2, ',', '.') }}</div>
                <div style="font-size: 8px; color: #6B7280; margin-top: 3px;">{{ $totals['qtd_total'] }} faturas</div>
            </td>
            <td class="total-card pendente" style="width: 25%;">
                <div class="label">Pendentes</div>
                <div class="value">R$ {{ number_format($totals['pendente'], 2, ',', '.') }}</div>
                <div style="font-size: 8px; color: #6B7280; margin-top: 3px;">{{ $totals['qtd_pendente'] }} faturas</div>
            </td>
            <td class="total-card pago" style="width: 25%;">
                <div class="label">Recebidas</div>
                <div class="value">R$ {{ number_format($totals['pago'], 2, ',', '.') }}</div>
                <div style="font-size: 8px; color: #6B7280; margin-top: 3px;">{{ $totals['qtd_pago'] }} faturas</div>
            </td>
            <td class="total-card cancelado" style="width: 25%;">
                <div class="label">Canceladas</div>
                <div class="value">R$ {{ number_format($totals['cancelado'], 2, ',', '.') }}</div>
                <div style="font-size: 8px; color: #6B7280; margin-top: 3px;">{{ $totals['qtd_cancelado'] }} faturas</div>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Cliente</th>
                <th>Descrição</th>
                <th>Serviço</th>
                <th>Vencimento</th>
                <th>Recebido em</th>
                <th class="text-right">Valor</th>
                <th>Forma Pag.</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if($data->count() > 0)
                @foreach($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->customer_name ?? '-' }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->service_name ?? '-' }}</td>
                    <td>{{ $item->date_due ? date('d/m/Y', strtotime($item->date_due)) : '-' }}</td>
                    <td>{{ $item->date_payment ? date('d/m/Y', strtotime($item->date_payment)) : '-' }}</td>
                    <td class="text-right">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                    <td>{{ $item->payment_method ?? '-' }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower($item->status) }}">
                            {{ $item->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        Nenhum registro encontrado
                    </td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr style="background-color: #1F2937; color: #FFFFFF; font-weight: bold;">
                <td colspan="6" class="text-right" style="padding: 10px;">TOTAL GERAL:</td>
                <td class="text-right" style="padding: 10px;">R$ {{ number_format($totals['total'], 2, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Este relatório foi gerado automaticamente pelo sistema Cobrança Segura</p>
        <p>Página 1 de 1 | Total de registros: {{ $data->count() }}</p>
    </div>

    <script>
        // Auto-gerar PDF quando a página carregar
        window.onload = function() {
            // Aguardar um pouco para garantir que tudo foi renderizado
            setTimeout(function() {
                const { jsPDF } = window.jspdf;
                
                // Criar instância do jsPDF em modo paisagem
                const doc = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: 'a4'
                });

                // Capturar o conteúdo HTML como imagem
                html2canvas(document.body, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    windowWidth: document.body.scrollWidth,
                    windowHeight: document.body.scrollHeight,
                    backgroundColor: '#FFFFFF'
                }).then(function(canvas) {
                    const imgData = canvas.toDataURL('image/png');
                    const imgWidth = 297; // A4 width in mm (landscape)
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    
                    let heightLeft = imgHeight;
                    let position = 0;

                    // Adicionar primeira página
                    doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= 210; // A4 height in mm

                    // Adicionar páginas adicionais se necessário
                    while (heightLeft > 0) {
                        position = heightLeft - imgHeight;
                        doc.addPage();
                        doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                        heightLeft -= 210;
                    }

                    // Salvar o PDF
                    doc.save('relatorio_contas_receber_' + new Date().getTime() + '.pdf');
                }).catch(function(error) {
                    console.error('Erro ao gerar PDF:', error);
                    // Fallback: mostrar opção de impressão
                    alert('Erro ao gerar PDF automaticamente. Use Ctrl+P para imprimir/salvar como PDF.');
                });
            }, 500);
        };
    </script>
</body>
</html>

