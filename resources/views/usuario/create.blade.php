@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Nuevo usuario</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @includeif('partials.errors')
            <div class="card-body">
                <form method="POST" action="{{ route('usuarios.store') }}" role="form">
                    @csrf
                    @include('usuario.form')
                </form>
             </div>
        </div>
    </div>
@stop
