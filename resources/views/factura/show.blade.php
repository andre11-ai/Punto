@extends('adminlte::page')
@section('title', 'Lista de Facturas')

@section('content_header')
    <h1>Lista de Facturas</h1>
@stop

@section('content')
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Folio</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($facturas as $factura)
        <tr>
            <td>{{ $factura->folio }}</td>
            <td>{{ $factura->venta->cliente->nombre ?? $factura->razon_social }}</td>
            <td>{{ $factura->fecha }}</td>
            <td>${{ number_format($factura->total,2) }}</td>
            <td>
                <a href="{{ route('factura.pdffactura', $factura->id) }}" class="btn btn-primary btn-sm" target="_blank">PDF</a>
                <form action="{{ route('factura.destroy', $factura->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button onclick="return confirm('¿Seguro que deseas eliminar esta factura?')" class="btn btn-danger btn-sm">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
