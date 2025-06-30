<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket Devolución #{{ $devolucion->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        .title { font-weight: bold; font-size: 16px; text-align: center; margin-bottom: 5px; }
        .line { border-bottom: 1px dashed #333; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 2px 5px; text-align: left; }
        th { background: #eee; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="title">TICKET DEVOLUCIÓN</div>
    <div>Devolución #: {{ $devolucion->id }}</div>
    <div>Venta original #: {{ $venta->id ?? '' }}</div>
    <div>Fecha: {{ $fecha->format('d/m/Y H:i') }}</div>
    <div>Usuario: {{ $usuario }}</div>
    <div class="line"></div>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant</th>
                <th>P.Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $prod)
            <tr>
                <td>{{ $prod->producto }}</td>
                <td>{{ $prod->cantidad }}</td>
                <td>${{ $prod->precio }}</td>
                <td>${{ $prod->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="line"></div>
    <div class="total">Total productos devueltos: {{ $total_productos }}</div>
    <div class="total">Total devolución: <span style="color:red;">-${{ $total }}</span></div>
    <div class="total">En letra: {{ $total_letras }}</div>
    @if($devolucion->motivo)
    <div><strong>Motivo:</strong> {{ $devolucion->motivo }}</div>
    @endif
    <div style="text-align:center; margin-top:15px;">Gracias por su preferencia</div>
</body>
</html>
