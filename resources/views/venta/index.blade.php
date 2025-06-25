@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Venta</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            {{ __('Nueva venta') }}
                        </span>
                        <div class="float-right">
                            <a href="{{ route('venta.show') }}" class="btn btn-primary btn-sm float-right"
                                data-placement="left">
                                {{ __('Listar ventas') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @livewire('product-list')
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    @stack('scripts')
@stop
