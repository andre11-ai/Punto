@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Ventas</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">

                        <span id="card_title">
                            {{ __('ventas') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover display responsive nowrap" width="100%"
                            id="tblVentas">
                            <thead class="thead">
                                <tr>
                                    <th>Id</th>
                                    <th>Monto</th>
                                    <th>Fecha/Hora</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th>

                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="{{asset('DataTables/datatables.min.css')}}" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endsection

@section('js')
    <script src="{{asset('DataTables/datatables.min.js')}}"></script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    new DataTable('#tblVentas', {
        responsive: true,
        fixedHeader: true,
        ajax: {
            url: '{{ route('sales.list') }}',
            dataSrc: 'data'
        },
columns: [
    { data: 'id' },
    { data: 'total' },
    { data: 'created_at' },
    { data: 'tipo' },
    {
        data: null,
        render: function(data, type, row) {
    return '<a class="btn btn-sm btn-primary" target="_blank" href="{{ url('/ventas/ticket') }}/' + row.id + '">Ticket</a>';
        }
    }
],
createdRow: function(row, data, dataIndex) {
    if (data.tipo.includes('Abono')) {
        $(row).addClass('table-warning');
    }
},
        language: {
            "decimal": ",",
            "thousands": ".",
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "No hay datos disponibles en esta tabla",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "loadingRecords": "Cargando...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        order: [[0, 'desc']]
    });
});
    </script>
@stop
