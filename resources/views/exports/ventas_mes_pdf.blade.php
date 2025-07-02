<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ventas por Mes</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Reporte de Ventas por Mes</h2>
    <table>
        <thead>
            <tr>
                <th>Año</th>
                <th>Mes</th>
                <th>Total Vendido</th>
                <th>Ventas Realizadas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $v)
                <tr>
                    <td>{{ $v->anio }}</td>
                    <td>
                        @php
                            $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
                        @endphp
                        {{ $meses[$v->mes] ?? $v->mes }}
                    </td>
                    <td>${{ number_format($v->total_vendido, 2) }}</td>
                    <td>{{ $v->ventas_realizadas }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
