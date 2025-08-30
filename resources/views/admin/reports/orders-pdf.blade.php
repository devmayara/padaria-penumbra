<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Pedidos - Padaria Penumbra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .summary-grid {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .summary-item {
            flex: 1;
            min-width: 150px;
            margin: 5px;
            text-align: center;
        }
        
        .summary-item h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #666;
        }
        
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .orders-by-status {
            margin-bottom: 30px;
        }
        
        .orders-by-status h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .status-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .status-item {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            min-width: 120px;
            text-align: center;
        }
        
        .status-item .count {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        
        .status-item .label {
            font-size: 12px;
            color: #666;
            text-transform: capitalize;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .orders-table th,
        .orders-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .orders-table th {
            background: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        
        .orders-table td {
            font-size: 11px;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: capitalize;
        }
        
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-pago { background: #d1ecf1; color: #0c5460; }
        .status-entregue { background: #d4edda; color: #155724; }
        .status-cancelado { background: #f8d7da; color: #721c24; }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Pedidos</h1>
        <p><strong>Padaria Penumbra</strong></p>
        <p>Período: {{ $startDate->format('d/m/Y') }} a {{ $endDate->format('d/m/Y') }}</p>
        <p>Gerado em: {{ $generatedAt->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <h2 style="margin: 0 0 15px 0; font-size: 18px; color: #333;">Resumo Geral</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <h3>Total de Pedidos</h3>
                <div class="value">{{ $orders->count() }}</div>
            </div>
            <div class="summary-item">
                <h3>Faturamento Total</h3>
                <div class="value">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <h3>Período</h3>
                <div class="value">{{ $startDate->diffInDays($endDate) + 1 }} dias</div>
            </div>
        </div>
    </div>

    <div class="orders-by-status">
        <h3>Pedidos por Status</h3>
        <div class="status-grid">
            @foreach($ordersByStatus as $status => $count)
            <div class="status-item">
                <div class="count">{{ $count }}</div>
                <div class="label">{{ $status }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="orders-table-section">
        <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
            Lista de Pedidos
        </h3>
        
        @if($orders->count() > 0)
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Data do Pedido</th>
                    <th>Data de Entrega</th>
                    <th>Data de Cancelamento</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>{{ $order->user->name }}</td>
                    <td>
                        <span class="status-badge status-{{ $order->status }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td><strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong></td>
                    <td>{{ $order->created_at ? Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $order->delivered_at ? Carbon\Carbon::parse($order->delivered_at)->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $order->cancelled_at ? Carbon\Carbon::parse($order->cancelled_at)->format('d/m/Y H:i') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="text-align: center; color: #666; font-style: italic; padding: 20px;">
            Nenhum pedido encontrado para o período selecionado.
        </p>
        @endif
    </div>

    <div class="footer">
        <p><strong>Padaria Penumbra</strong> - Sistema de Gestão</p>
        <p>Relatório gerado automaticamente em {{ $generatedAt->format('d/m/Y H:i:s') }}</p>
        <p>Página 1 de 1</p>
    </div>
</body>
</html>
