<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Productos Vendidos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Reporte de Productos Vendidos</h2>
    <table>
        <thead>
            <tr>
                <th>ID Producto</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Unidades Vendidas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $p)
                <tr>
                    <td>{{ $p->id_producto }}</td>
                    <td>{{ $p->producto }}</td>
                    <td>{{ $p->categoria }}</td>
                    <td>{{ $p->cantidad_vendida }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
