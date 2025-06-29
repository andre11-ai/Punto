<!DOCTYPE html>
<html>
<head>
    <title>Ticket de Abono</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
        body { padding: 10px; }
        .ticket { width: 80mm; padding: 10px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #333; }
        .logo { max-width: 80px; margin-bottom: 10px; }
        .business-name { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .business-info { font-size: 12px; color: #555; margin-bottom: 5px; }
        .ticket-info { margin: 15px 0; font-size: 13px; }
        .ticket-info p { margin: 3px 0; }
        .divider { border-top: 1px dashed #333; margin: 10px 0; }
        .items-table { width: 100%; table-layout: fixed; border-collapse: collapse; margin: 10px 0; font-size: 12px; }
        .items-table th { text-align: left; padding: 5px 0; border-bottom: 1px solid #ddd; }
        .items-table td { padding: 5px 0; border-bottom: 1px solid #eee; }
        .items-table .text-right { text-align: right; }
        .total-section { margin-top: 15px; font-size: 14px; }
        .total-row { display: flex; justify-content: space-between; margin: 5px 0; }
        .total-label, .total-value { font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 11px; color: #777; }
        .thank-you { margin-top: 15px; font-style: italic; text-align: center; }
        .payment-method { margin: 10px 0; padding: 8px; background: #f5f5f5; border-radius: 4px; font-size: 13px; }
        .logo {
    text-align: center;
    margin-bottom: 5px;
}
.logo img {
    max-width: 70px;
    max-height: 70px;
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 5px;
}
    </style>
<body>
    <div class="ticket">
        <div class="header">
    <img src="{{ public_path('storage/img/Logo-Colo.png') }}" alt="Logo" style="display: block; margin: 0 auto 5px auto; max-width: 70px; max-height: 70px;">
            <div class="business-name">{{ $company->nombre }}</div>
            <div class="business-info">{{ $company->direccion }}</div>
            <div class="business-info">Tel: {{ $company->telefono }}</div>
            <div class="business-info"><strong>TICKET DE ABONO</strong></div>
        </div>

        <div class="divider"></div>

        <div>
            <strong>Fecha:</strong> {{ $fecha->format('Y-m-d H:i') }}<br>
<strong>Cliente:</strong> {{ $cliente->nombre }}<br>
        </div>

        <div class="payment-method">
            <strong>Método de pago:</strong> {{ strtoupper($venta->metodo_pago ?? 'EFECTIVO') }}
        </div>

        <div class="total-section">
            <div class="total-row">
<strong>Deuda original:</strong> ${{ number_format($deuda_original, 2) }}
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
