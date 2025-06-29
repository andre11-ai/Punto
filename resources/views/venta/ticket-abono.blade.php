<!DOCTYPE html>
<html>
<head>
    <title>Ticket de Abono</title>
    <style>
        body { margin:0; padding:0; }
        .ticket {
            width: 250px;
            margin: 0 auto;
            font-size: 13px;
            font-family: Arial, sans-serif;
            padding: 10px;
        }
        .header { text-align: center; margin-bottom: 10px; }
        .logo { max-width: 70px; margin-bottom: 10px; }
        .business-name { font-size: 18px; font-weight: bold; }
        .business-info { font-size: 12px; color: #555; }
        .divider { border-top: 1px dashed #333; margin: 10px 0; }
        .total-section { margin-top: 10px; }
        .total-row { display: flex; justify-content: space-between; margin: 3px 0; }
        .total-label, .total-value { font-weight: bold; }
        .payment-method { margin: 10px 0; padding: 6px; background: #f5f5f5; border-radius: 4px; font-size: 14px; }
        .thank-you { margin-top: 15px; font-style: italic; text-align: center; }
        .footer { margin-top: 10px; text-align: center; font-size: 11px; color: #777; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <img src="{{ $logoPath }}" class="logo" alt="Logo">
            <div class="business-name">{{ $company->nombre }}</div>
            <div class="business-info">{{ $company->direccion }}</div>
            <div class="business-info">Tel: {{ $company->telefono }}</div>
            <div class="business-info"><strong>TICKET DE ABONO</strong></div>
        </div>

        <div class="divider"></div>

        <div>
            <strong>Fecha:</strong> {{ $fecha->format('Y-m-d H:i') }}<br>
            <strong>Cliente:</strong> {{ $cliente?->nombre ?? 'Cliente eliminado' }}
        </div>

        <div class="payment-method">
            <strong>Método de pago:</strong> {{ strtoupper($venta->metodo_pago ?? 'EFECTIVO') }}
        </div>

        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Deuda original:</span>
                <span class="total-value">${{ number_format($deuda_original, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Deuda anterior:</span>
                <span class="total-value">${{ number_format($deuda_anterior, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Abono realizado:</span>
                <span class="total-value">${{ number_format($abono_realizado, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Deuda restante:</span>
                <span class="total-value">${{ number_format($deuda_restante, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Productos vendidos:</span>
                <span class="total-value">{{ $productos_vendidos }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Pago recibido:</span>
                <span class="total-value">${{ number_format($pago_recibido, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Cambio:</span>
                <span class="total-value">${{ number_format($cambio, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">En letras:</span>
                <span class="total-value">{{ $total_en_letras }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="thank-you">
            ¡Gracias por su pago!
        </div>

        <div class="footer">
            {{ $company->mensaje_ticket ?? 'Este ticket es su comprobante' }}<br>
            {{ $fecha->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
