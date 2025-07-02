@extends('adminlte::page')
@section('title', 'Generar Factura')

@section('content_header')
    <h1>Generar Factura Venta #{{ $venta->id }}</h1>
@stop

@section('content')
<form method="POST" action="{{ route('factura.store') }}">
    @csrf
    <input type="hidden" name="venta_id" value="{{ $venta->id }}">
    <div class="mb-3">
        <label for="rfc">RFC</label>
        <input type="text" name="rfc" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="razon_social">Razón Social</label>
        <input type="text" name="razon_social" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="uso_cfdi">Uso CFDI</label>
        <input type="text" name="uso_cfdi" class="form-control" required placeholder="G03, P01, etc.">
    </div>
    <button class="btn btn-primary">Generar Factura</button>
</form>
@stop
