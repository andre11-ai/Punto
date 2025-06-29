@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Ventas</h1>
@stop

@section('css')
    <!-- DataTables, Custom CSS, Font Awesome, Bootstrap Icons -->
    <link href="{{ asset('DataTables/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        #tblVentas {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        #tblVentas thead th {
            background-color: #3490dc;
            color: white;
            font-weight: 500;
        }
        #tblVentas tbody tr:hover {
            background-color: rgba(52, 144, 220, 0.1);
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 4px;
        }
        .table-warning {
            background-color: #fff3cd !important;
        }
        .card-header.bg-primary {
            background-color: #3490dc !important;
            color: white !important;
        }

        /* PAGINATION DESIGN LIKE IMAGE 3 */
        .dataTables_wrapper .dataTables_paginate {
            text-align: right !important;
            padding-top: 10px;
            margin-bottom: 5px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: none !important;
            border: 1px solid #e1e4e8 !important;
            color: #2095f2 !important;
            border-radius: 0 !important;
            margin: 0 !important;
            min-width: 38px;
            min-height: 32px;
            line-height: 32px;
            padding: 0 12px !important;
            font-size: 1em;
            box-sizing: border-box;
            transition: background 0.2s, color 0.2s;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0094ff !important;
            color: #fff !important;
            font-weight: bold;
            box-shadow: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background: #e6f3ff !important;
            color: #2095f2 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: none !important;
            color: #bcbcbc !important;
            border: 1px solid #e1e4e8 !important;
            cursor: not-allowed !important;
        }
        .dataTables_wrapper .dataTables_paginate .ellipsis {
            border: none !important;
            background: none !important;
            color: #bcbcbc !important;
            padding: 0 8px !important;
            min-width: 24px;
        }
        /* Remove double line when multiple paginations */
        .dataTables_wrapper .dataTables_paginate {
            border: none !important;
        }

        /* SweetAlert - Match Alert Style */
        .alert-success {
            border-left: 4px solid #28a745;
        }
        .alert-success strong {
            color: #28a745;
        }
        .btn-close {
            filter: invert(1);
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            <i class="fas fa-cash-register me-2"></i>{{ __('Ventas') }}
                        </span>
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
                        <table class="table table-striped table-hover display responsive nowrap" width="100%" id="tblVentas">
                            <thead class="thead">
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>Id</th>
                                    <th><i class="fas fa-dollar-sign me-1"></i>Monto</th>
                                    <th><i class="fas fa-clock me-1"></i>Fecha/Hora</th>
                                    <th><i class="fas fa-tags me-1"></i>Tipo</th>
                                    <th><i class="fas fa-cogs me-1"></i>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán mediante DataTables -->
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
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            const table = new DataTable('#tblVentas', {
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
                            return `
                                <a class="btn btn-sm btn-primary btn-action" target="_blank" href="{{ url('/ventas/ticket') }}/${row.id}">
                                    <i class="fas fa-receipt"></i> Ticket
                                </a>
                                <form id="delete-form-${row.id}" action="{{ url('ventas') }/${row.id}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-action btn-delete" data-id="${row.id}">
                                        <i class="fas fa-trash-alt me-1"></i>Eliminar
                                    </button>
                                </form>
                            `;
                        }
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    if (data.tipo && data.tipo.includes('Abono')) {
                        $(row).addClass('table-warning');
                    }
                },
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                }
            });

            // Eliminar venta con SweetAlert
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const ventaId = $(this).data('id');
                const form = $(this).closest('form');

                Swal.fire({
                    title: '¿Eliminar venta?',
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
                            '¡Eliminada!',
                            'La venta ha sido eliminada.',
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
