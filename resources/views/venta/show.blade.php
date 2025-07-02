@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1><i class="fas fa-cash-register me-2"></i>Ventas</h1>
@stop

@section('css')
    <link href="{{ asset('DataTables/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .card-header {
            background-color: #3490dc !important;
            color: white !important;
        }
        .btn-primary {
            background-color: #3490dc;
            border-color: #2a7aaf;
        }
        .btn-primary:hover {
            background-color: #2a7aaf;
            border-color: #226089;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #e0a800;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
        .alert-success {
            border-left: 4px solid #28a745;
        }
        .alert-success strong {
            color: #28a745;
        }
        .btn-close {
            filter: invert(1);
        }
        /* Pagination styling */
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
        .dataTables_wrapper .dataTables_paginate {
            border: none !important;
        }
        /* Estilo para los modales de devoluciones */
        .modal-devolucion .modal-header {
            background-color: #ffc107;
            color: #212529;
        }
        .modal-devolucion .modal-title {
            font-weight: 600;
        }
        .modal-devolucion .badge {
            font-size: 0.85em;
            padding: 0.35em 0.65em;
        }
        .modal-devolucion .table th {
            background-color: #f8f9fa;
        }
        .modal-devolucion .list-group-item {
            border-left: 3px solid #ffc107;
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
                            <i class="fas fa-cash-register me-2"></i>{{ __('Ventas') }}
                        </span>
                        <button id="btnListarDevolucionesArriba" class="btn btn-warning">
                            <i class="fa fa-list me-1"></i> Listar devoluciones
                        </button>
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
                                    <th><i class="fas fa-hashtag me-1"></i>ID</th>
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

    <!-- Modal Devolución -->
    <div class="modal fade modal-devolucion" id="modalDevolucion" tabindex="-1" aria-labelledby="modalDevolucionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formDevolucion" method="POST">
                    @csrf
                    <input type="hidden" name="venta_id" id="venta_id_devolucion" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDevolucionLabel"><i class="fas fa-undo me-2"></i>Devolución de venta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo de la devolución (opcional):</label>
                            <input type="text" name="motivo" id="motivo" class="form-control" maxlength="255">
                        </div>
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <i class="fas fa-boxes me-2"></i>Productos de la venta
                            </div>
                            <div class="card-body p-0">
                                <div id="devolucionProductos">
                                    <!-- Aquí se cargan los productos por JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fa fa-undo me-1"></i> Procesar devolución
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Listar Devoluciones -->
    <div class="modal fade modal-devolucion" id="modalListarDevoluciones" tabindex="-1" aria-labelledby="modalListarDevolucionesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalListarDevolucionesLabel"><i class="fas fa-list me-2"></i>Devoluciones realizadas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoDevoluciones">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchDevoluciones" class="form-control" placeholder="Buscar devoluciones...">
                    </div>
                    <div id="listaDevoluciones">
                        <!-- Aquí se cargan las devoluciones por JS -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

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
                            let devolverBtn = '';
                            let ticketDevolucionBtn = '';

                            if (row.tipo === 'venta' && !row.tiene_devolucion) {
                                devolverBtn = `
                                    <button class="btn btn-warning btn-sm btn-action btn-devolucion" data-id="${row.id}">
                                        <i class="fa fa-undo me-1"></i> Devolucion
                                    </button>
                                `;
                            }

                            return `
                                <a class="btn btn-sm btn-primary btn-action" target="_blank" href="{{ url('/ventas/ticket') }}/${row.id}">
                                    <i class="fas fa-receipt me-1"></i> Ticket
                                </a>
                                ${devolverBtn}
                                ${ticketDevolucionBtn}
                                <form id="delete-form-${row.id}" action="/ventas/${row.id}" method="POST" style="display:inline-block;">
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
                    if ((data.tipo && data.tipo.includes('Abono')) || data.tiene_devolucion) {
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

            $(document).on('click', '.btn-devolucion', function() {
                const ventaId = $(this).data('id');
                window.idVentaActualParaModal = ventaId;
                $('#venta_id_devolucion').val(ventaId);

                $.get(`/ventas/${ventaId}/detalles-json`, function(response) {
                    let html = `
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad comprada</th>
                                    <th>Cantidad a devolver</th>
                                    <th>Precio unitario</th>
                                    <th>¿Se puede devolver?</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    response.detalles.forEach((detalle, idx) => {
                        html += `
                            <tr>
                                <td>${detalle.producto.nombre}</td>
                                <td>${detalle.cantidad}</td>
                                <td>
                                    <input type="number" name="productos[${idx}][cantidad]" min="0" max="${detalle.cantidad}" value="0" class="form-control" ${detalle.se_puede_devolver === 'NO' ? 'disabled' : ''}>
                                    <input type="hidden" name="productos[${idx}][producto_id]" value="${detalle.producto.id}">
                                    <input type="hidden" name="productos[${idx}][precio]" value="${detalle.precio}">
                                </td>
                                <td>$${detalle.precio}</td>
                                <td>
                                    <span class="badge ${detalle.se_puede_devolver === 'SI' ? 'bg-success' : 'bg-danger'}">
                                        ${detalle.se_puede_devolver === 'SI' ? 'SI' : 'NO'}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });
                    html += `</tbody></table>`;
                    $('#devolucionProductos').html(html);
                    $('#formDevolucion').attr('action', `/ventas/${ventaId}/devolucion`);
                    $('#modalDevolucion').modal('show');
                });
            });

            $('#formDevolucion').on('submit', function(e){
                e.preventDefault();

                $('#devolucionProductos tbody tr').each(function() {
                    const sePuede = $(this).find('span.badge').text().trim();
                    const inputCantidad = $(this).find('input[name$="[cantidad]"]');
                    if (sePuede === 'NO' || !inputCantidad.val() || parseInt(inputCantidad.val()) === 0) {
                        inputCantidad.prop('disabled', true);
                        $(this).find('input[type="hidden"]').prop('disabled', true);
                    }
                });

                $.post($(this).attr('action'), $(this).serialize())
                    .done(function(resp){
                        Swal.fire('Éxito','Devolución procesada','success').then(()=>window.location.reload());
                    })
                    .fail(function(xhr){
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error en la devolución','error');
                    });
            });

            $('#btnListarDevolucionesArriba').on('click', function() {
                $.get('/devoluciones/todas', function(response) {
                    let html = '';
                    if (response.length === 0) {
                        html = '<div class="alert alert-info">No hay devoluciones registradas.</div>';
                    } else {
                        html = `
                            <div class="list-group">
                        `;
                        response.forEach(dev => {
                            let productos = dev.detalles.map(det =>
                                `<li class="list-group-item">${det.producto?.nombre || det.producto?.producto || 'Producto eliminado'} (${det.cantidad})</li>`
                            ).join('');

                            html += `
                                <div class="list-group-item mb-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1"><strong>Fecha:</strong> ${dev.created_at}</h6>
                                        <small class="text-muted">Usuario: ${dev.user?.name || 'Desconocido'}</small>
                                    </div>
                                    <p class="mb-1"><strong>Motivo:</strong> ${dev.motivo || 'Sin motivo especificado'}</p>
                                    <div class="mb-2">
                                        <strong>Productos devueltos:</strong>
                                        <ul class="list-group list-group-flush mt-2">
                                            ${productos}
                                        </ul>
                                    </div>
                                    <div class="text-end">
                                        <a href="/devoluciones/${dev.id}/ticket" target="_blank" class="btn btn-warning btn-sm">
                                            <i class="fas fa-file-pdf me-1"></i> Ticket devolución
                                        </a>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    }
                    $('#listaDevoluciones').html(html);

                    // Configurar búsqueda
                    $('#searchDevoluciones').on('keyup', function() {
                        const value = $(this).val().toLowerCase();
                        $('.list-group-item').filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                        });
                    });

                    var modalDevoluciones = new bootstrap.Modal(document.getElementById('modalListarDevoluciones'));
                    modalDevoluciones.show();
                });
            });
        });
    </script>
@endsection
