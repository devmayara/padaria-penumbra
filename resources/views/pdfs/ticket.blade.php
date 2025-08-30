<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha #{{ $ticket->ticket_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0 20px;
            font-size: 14px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 2px;
        }
        .logo {
            font-size: 20px;
            font-weight: bold;
        }
        .ticket-info {
            margin-bottom: 15px;
            text-align: center;
        }
        .ticket-info strong {
            display: inline-block;
            margin: 0 10px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            border-bottom: 1px dashed #999;
            padding: 6px;
            text-align: left;
        }
        .items-table th {
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        .qr-code {
            text-align: center;
            margin-top: 5px;
        }
        .qr-code img {
            max-width: 60px;
            height: 60px;
        }
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <div class="logo">PADARIA PENUMBRA</div>
    </div>

    <!-- Informações básicas -->
    <div class="ticket-info">
        <strong>{{ now()->format('d/m/Y H:i') }}</strong>
    </div>

    <!-- Itens -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Qtd</th>
                <th>Produto</th>
                <th>Preço</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ticket->order->items as $item)
                <tr>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <div class="total">
        Total: {{ $ticket->order->formatted_total_amount }}
    </div>

    <!-- QR Code -->
    @if(isset($qrCodeHtml) && !empty($qrCodeHtml))
        <div class="qr-code">
            <div style="text-align: center; margin: 15px 0;">
                {!! $qrCodeHtml !!}
            </div>
            <p style="margin: 10px 0 0 0; font-weight: bold; text-align: center;">{{ $ticket->ticket_number }}</p>
            <p style="margin: 5px 0 0 0; font-size: 10px; color: #666; text-align: center;">Escaneie para acessar a ficha digitalmente</p>
        </div>
    @else
        <div class="qr-code">
            <p style="margin: 10px 0 0 0; font-weight: bold; text-align: center;">{{ $ticket->ticket_number }}</p>
            <p style="margin: 5px 0 0 0; font-size: 10px; color: #666; text-align: center;">QR Code não disponível</p>
        </div>
    @endif
</body>
</html>
