@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1><i class="fas fa-truck me-2"></i>Proveedores</h1>
@stop

@section('css')
    <link href="DataTables/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        #tblproveedores {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        #tblproveedores thead th {
            background-color: #3490dc;
            color: white;
            font-weight: 500;
        }

        #tblproveedores tbody tr:hover {
            background-color: rgba(52, 144, 220, 0.1);
        }

        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
        }

        .card-header {
            background-color: #3490dc !important;
            color: white !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .alert-success {
            border-left: 4px solid #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }

        .alert-success strong {
            color: #28a745;
        }

        .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        .btn-close:hover {
            opacity: 1;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            <i class="fas fa-truck-loading me-2"></i>{{ __('Proveedores') }}
                        </span>

                        <div class="float-right">
                            <a href="{{ route('proveedores.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus-circle me-1"></i>{{ __('Nuevo Proveedor') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>{{ $message }}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover display responsive nowrap" width="100%" id="tblproveedores">
                            <thead class="thead">
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                    <th><i class="fas fa-building me-1"></i>Nombre</th>
                                    <th><i class="fas fa-barcode me-1"></i>UPC</th>
                                    <th><i class="fas fa-cogs me-1"></i>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($proveedores as $proveedor)
                                    <tr>
                                        <td>{{ $proveedor->id }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $proveedor->nombre }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $proveedor->upc }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-sm btn-primary btn-action" href="{{ route('proveedores.edit', $proveedor->id) }}">
                                                    <i class="fas fa-edit me-1"></i>Editar
                                                </a>

                                                <form id="delete-form-{{ $proveedor->id }}" action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger btn-action btn-delete" data-id="{{ $proveedor->id }}">
                                                        <i class="fas fa-trash-alt me-1"></i>Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="empty-state">
                                            <i class="fas fa-box-open"></i>
                                            <h5 class="mt-2">No hay proveedores registrados</h5>
                                            <p class="text-muted">Comienza agregando un nuevo proveedor</p>
                                            <a href="{{ route('proveedores.create') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-plus me-1"></i>Agregar Proveedor
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="DataTables/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Configuración de DataTables
            const table = new DataTable('#tblproveedores', {
                responsive: true,
                fixedHeader: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                },
                order: [[0, 'desc']],
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                }
            });

            // Eliminar proveedor con SweetAlert
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const proveedorId = $(this).data('id');
                const form = $(this).closest('form');

                Swal.fire({
                    title: '¿Eliminar proveedor?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    backdrop: true,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: form.attr('action'),
                            type: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                return response;
                            },
                            error: function(xhr) {
                                Swal.showValidationMessage(
                                    `Error: ${xhr.statusText}`
                                );
                            }
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire(
                            '¡Eliminado!',
                            'El proveedor ha sido eliminado.',
                            'success'
                        ).then(() => {
                            table.row(form.parents('tr')).remove().draw();
                        });
                    }
                });
            });
        });
    </script>
@endsection
