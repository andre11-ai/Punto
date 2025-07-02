<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ticket Factura #{{ $factura->folio }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #222;
        }
        .ticket-container {
            width: 320px;
            margin: 0 auto;
            padding: 8px 2px;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .logo {
            max-width: 60px;
            margin-bottom: 5px;
        }
        .business-name {
            font-size: 1.1em;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .business-info {
            font-size: 11px;
            color: #444;
        }
        .factura-title {
            font-weight: bold;
            font-size: 1em;
            margin: 12px 0 5px 0;
        }
        .ticket-info, .cliente-info {
            font-size: 11px;
            margin-bottom: 3px;
        }
        .label {
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        .table th, .table td {
            border: 1px solid #bbb;
            padding: 3px 4px;
            font-size: 11px;
        }
        .table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: left;
        }
        .table td {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .totales {
            margin-top: 8px;
            font-size: 12px;
        }
        .tot-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-weight: bold;
        }
        .cfdi {
            margin-top: 8px;
            font-size: 10.5px;
            text-align: center;
        }
        .footer {
            margin-top: 12px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .divider {
            border-top: 1px dashed #888;
            margin: 7px 0;
        }
        .small {
            font-size: 10px;
            color: #888;
        }
        @page { margin: 0; }
        html, body { width: 100%; }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="header">
            @if(file_exists(public_path('storage/img/Logo-Colo.png')))
                <img src="{{ public_path('storage/img/Logo-Colo.png') }}" alt="Logo" class="logo">
            @endif
            <div class="business-name">ABARROTES ADS S. A. DE C. V.</div>
            <div class="business-info">RFC: ADS250211X3B</div>
            <div class="business-info">Av. Juan de Dios Bátiz, Nueva Industrial Vallejo, Gustavo A. Madero, 07320, CDMX</div>
        </div>

        <div class="factura-title">Factura electrónica CFDI 4.0</div>
        <div class="divider"></div>

        <div class="ticket-info">
            <span class="label">Folio:</span> {{ $factura->folio }}<br>
            <span class="label">Fecha:</span> {{ $factura->fecha }}<br>
            <span class="label">Lugar de expedición:</span> 07320
        </div>

        <div class="cliente-info">
            <span class="label">Cliente:</span> {{ $factura->razon_social }}<br>
            RFC: {{ $factura->rfc }}<br>
            Uso CFDI: {{ $factura->uso_cfdi }}
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Cant</th>
                    <th>Descripción</th>
                    <th>P.Unit</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
            @foreach($factura->venta->detalles as $detalle)
                <tr>
                    <td class="text-right">{{ $detalle->cantidad }}</td>
                    <td>
                        {{ $detalle->producto->producto ?? 'Producto eliminado' }}
                    </td>
                    <td class="text-right">${{ number_format($detalle->precio, 2) }}</td>
                    <td class="text-right">${{ number_format($detalle->precio * $detalle->cantidad, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="totales">
            <div class="tot-row">
                <span>Subtotal:</span>
                <span>${{ number_format($factura->total, 2) }}</span>
            </div>
            <div class="tot-row">
                <span>IVA 16%:</span>
                <span>${{ number_format($factura->total * 0.16, 2) }}</span>
            </div>
            <div class="tot-row">
                <span>Total:</span>
                <span>${{ number_format($factura->total * 1.16, 2) }}</span>
            </div>
        </div>

        <div class="cfdi">
            <b>Forma de pago:</b> 03 – Transferencia<br>
            <b>Método:</b> PUE – Pago en una sola exhibición
        </div>

        <div class="divider"></div>

        <div class="footer">
            Este ticket es una representación impresa del CFDI.<br>
            UUID: ...UUID DE EJEMPLO...<br>
            Sello digital CFDI: ...TRUNCADO...<br>
            Sello del SAT: ...TRUNCADO...<br>
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
