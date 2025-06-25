@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Proveedores</h1>
@stop

@section('content')
    <div class="">
        <div class="col-md-12">

            @includeif('partials.errors')

            <div class="card card-default">

                    <form action="{{ route('proveedores.update', $proveedor->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('proveedor.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
