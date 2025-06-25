@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Proveedores</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @includeif('partials.errors')
            <div class="card-body">
                <form method="POST" action="{{ route('proveedores.store') }}" role="form">
                    @csrf
                    @include('proveedor.form')
                </form>
            </div>
        </div>
    </div>
@stop
