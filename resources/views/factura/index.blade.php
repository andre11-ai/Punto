@extends('adminlte::page')

@section('title', 'Facturación')

@section('content_header')
    <h1><i class="fas fa-file-invoice-dollar me-2"></i>Facturación</h1>
@stop

@section('css')
    <link href="{{ asset('DataTables/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        #tblFacturacion thead th {
            background-color: #3490dc;
            color: white;
            font-weight: 500;
        }
        #tblFacturacion tbody tr:hover {
            background-color: rgba(52, 144, 220, 0.08);
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 4px;
        }
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        <i class="fas fa-file-invoice-dollar me-2"></i>{{ __('Facturación') }}
                    </span>
                    <form method="GET" class="d-flex" style="gap: 0.5rem;">
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar venta o cliente..." class="form-control form-control-sm" style="max-width: 220px;">
                        <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><strong>{{ $message }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" id="tblFacturacion">
                        <thead>
                            <tr>
                                <th>ID Venta</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Factura</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventas as $venta)
                            <tr>
                                <td>{{ $venta->id }}</td>
                                <td>{{ $venta->cliente->nombre ?? 'Sin cliente' }}</td>
                                <td>${{ number_format($venta->total,2) }}</td>
                                <td>{{ $venta->created_at }}</td>
                                <td>
                                    @if($venta->factura)
                                        <a href="{{ route('factura.show', $venta->factura->id) }}" class="btn btn-info btn-sm btn-action">
                                            <i class="fas fa-eye me-1"></i> Ver Factura
                                        </a>
                                    @else
                                        <a href="{{ route('factura.create', $venta->id) }}" class="btn btn-success btn-sm btn-action">
                                            <i class="fas fa-plus me-1"></i> Generar Factura
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $ventas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#tblFacturacion').DataTable({
                responsive: true,
                paging: false,     // Laravel paginación
                searching: false,  // Laravel búsqueda
                info: false,
                ordering: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                }
            });
        });
    </script>
@endsection
