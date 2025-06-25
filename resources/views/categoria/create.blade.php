@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Categoria</h1>
@stop

@section('content')
    <div class="row">
        <dv class="col-md-12">
            @includeif('partials.errors')
            <div class="card-body">
                <form method="POST" action="{{ route('categorias.store') }}" role="form">
                    @csrf
                    @include('categoria.form', ['categoria' => $categoria, 'proveedores' => $proveedores])

                </form>
            </div>
        </div>
    </div>
@stop
