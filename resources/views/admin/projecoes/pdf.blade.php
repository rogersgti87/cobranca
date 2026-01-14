<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Projeções Futuras</title>
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
            background: linear-gradient(135deg, #06b8f7 0%, #0284c7 100%);
            color: #FFFFFF;
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
            background-color: #F0F9FF;
            border-radius: 6px;
            border: 1px solid rgba(6,184,247,0.3);
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

        .total-card.projecoes .value {
            color: #06b8f7;
        }

        .total-card.media .value {
            color: #22C55E;
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
        <h1>Relatório de Projeções Futuras</h1>
        <p>Gerado em: {{ date('d/m/Y H:i:s') }} | Usuário: {{ $user->name ?? 'N/A' }}</p>
    </div>

    <div class="info-section">
        <h2>Filtros Aplicados</h2>
        <div class="info-row">
            <span><strong>Período de Projeção:</strong> {{ $filters['months_ahead'] ?? 12 }} meses</span>
        </div>
        @if(!empty($filters['recurrence_period']) && is_array($filters['recurrence_period']))
        <div class="info-row">
            <span><strong>Período de Recorrência:</strong> {{ implode(', ', $filters['recurrence_period']) }}</span>
        </div>
        @endif
        @if(!empty($filters['category']) && is_array($filters['category']))
        <div class="info-row">
            <span><strong>Categorias:</strong> {{ count($filters['category']) }} selecionada(s)</span>
        </div>
        @endif
        @if(!empty($filters['supplier']) && is_array($filters['supplier']))
        <div class="info-row">
            <span><strong>Fornecedores:</strong> {{ count($filters['supplier']) }} selecionado(s)</span>
        </div>
        @endif
    </div>

    @php
        $uniqueMonths = [];
        foreach($projections as $projection) {
            $monthKey = \Carbon\Carbon::parse($projection['date_due'])->format('Y-m');
            if(!in_array($monthKey, $uniqueMonths)) {
                $uniqueMonths[] = $monthKey;
            }
        }
        $monthsCount = count($uniqueMonths) ?: 1;
        $mediaMensal = $totalAmount / $monthsCount;
    @endphp

    <table class="totals-grid" style="width: 100%; border-collapse: separate; border-spacing: 10px;">
        <tr>
            <td class="total-card projecoes" style="width: 33.33%;">
                <div class="label">Total Projetado</div>
                <div class="value">R$ {{ number_format($totalAmount, 2, ',', '.') }}</div>
                <div style="font-size: 8px; color: #6B7280; margin-top: 3px;">{{ count($projections) }} projeções</div>
            </td>
            <td class="total-card media" style="width: 33.33%;">
                <div class="label">Média Mensal</div>
                <div class="value">R$ {{ number_format($mediaMensal, 2, ',', '.') }}</div>
                <div style="font-size: 8px; color: #6B7280; margin-top: 3px;">{{ $monthsCount }} meses</div>
            </td>
            <td class="total-card" style="width: 33.33%;">
                <div class="label">Período</div>
                <div class="value">{{ $filters['months_ahead'] ?? 12 }} meses</div>
                <div style="font-size: 8px; color: #6B7280; margin-top: 3px;">Projeções futuras</div>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Fornecedor</th>
                <th>Descrição</th>
                <th>Categoria</th>
                <th>Recorrência</th>
                <th>Vencimento</th>
                <th class="text-right">Valor</th>
                <th>Forma Pag.</th>
            </tr>
        </thead>
        <tbody>
            @if(count($projections) > 0)
                @foreach($projections as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['supplier_name'] ?? '-' }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ $item['category_name'] ?? '-' }}</td>
                    <td>{{ $item['recurrence_period'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($item['date_due'])->format('d/m/Y') }}</td>
                    <td class="text-right">R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                    <td>{{ $item['payment_method'] ?? '-' }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        Nenhuma projeção encontrada
                    </td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr style="background-color: #1F2937; color: #FFFFFF; font-weight: bold;">
                <td colspan="6" class="text-right" style="padding: 10px;">TOTAL GERAL:</td>
                <td class="text-right" style="padding: 10px;">R$ {{ number_format($totalAmount, 2, ',', '.') }}</td>
                <td style="padding: 10px;">{{ count($projections) }} projeções</td>
            </tr>
        </tfoot>
    </table>

    @if(count($monthlyTotals) > 0)
    <h2 style="font-size: 14px; font-weight: bold; margin: 20px 0 10px 0; color: #1F2937;">Total por Mês</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Mês</th>
                <th class="text-right">Total</th>
                <th class="text-center">Quantidade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyTotals as $monthData)
            <tr>
                <td>{{ $monthData['month'] }}</td>
                <td class="text-right">R$ {{ number_format($monthData['total'], 2, ',', '.') }}</td>
                <td class="text-center">{{ $monthData['count'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>Este relatório foi gerado automaticamente pelo sistema de gestão de cobranças.</p>
        <p>As projeções são baseadas em contas recorrentes cadastradas e podem variar conforme alterações futuras.</p>
    </div>

    <script>
        window.onload = function() {
            // Aguardar um pouco para garantir que tudo foi renderizado
            setTimeout(function() {
                // Usar html2canvas para capturar a página
                html2canvas(document.body, {
                    scale: 2,
                    useCORS: true,
                    logging: false
                }).then(function(canvas) {
                    const imgData = canvas.toDataURL('image/png');
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF('p', 'mm', 'a4');

                    const imgWidth = 210; // A4 width in mm
                    const pageHeight = 297; // A4 height in mm
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
                    doc.save('projecoes_futuras_' + new Date().getTime() + '.pdf');
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
