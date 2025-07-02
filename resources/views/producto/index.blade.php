@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Productos</h1>
@stop

@section('css')
    <link href="DataTables/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>

        #tblProducts {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        #tblProducts thead th {
            background-color: #3490dc;
            color: white;
            font-weight: 500;
        }

        #tblProducts tbody tr:hover {
            background-color: rgba(52, 144, 220, 0.1);
        }

        .badge-promo-2x1 {
            background-color: #f39c12;
            color: white;
        }

        .badge-promo-3x2 {
            background-color: #e74c3c;
            color: white;
        }

        .badge-promo-50 {
            background-color: #3498db;
            color: white;
        }

        .badge-promo-especial {
            background-color: #9b59b6;
            color: white;
        }

        .badge-promo-30 {
            background-color: #1abc9c;
            color: white;
        }

        #tblPromocionesVigentes {
            font-size: 0.9rem;
        }

        #tblPromocionesVigentes thead th {
            background-color: #f8f9fa;
            font-weight: 500;
        }

        #tblPromocionesVigentes tbody tr {
            transition: all 0.2s;
        }

        #tblPromocionesVigentes tbody tr:hover {
            background-color: rgba(52, 144, 220, 0.05);
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 4px;
        }

        .badge-activa {
            background-color: #28a745;
            color: white;
        }

        .badge-expirada {
            background-color: #dc3545;
            color: white;
        }

        .fecha-promocion {
            font-weight: 500;
        }

        .fecha-promocion.activa {
            color: #28a745;
        }

        .fecha-promocion.expirada {
            color: #dc3545;
        }

        .btn-promociones-vigentes {
            background-color: #ff6600;
            border-color: #cc5200;
            color: white;
        }

        .btn-promociones-vigentes:hover {
            background-color: #cc5200;
            border-color: #cc4900;
            color: white;
        }

        .tree-view {
            margin-top: 15px;
        }

        .tree-view ul {
            list-style-type: none;
            padding-left: 20px;
        }

        .tree-view li {
            margin-bottom: 5px;
            position: relative;
        }

        .tree-view .list-group-item {
            border-left: 3px solid #3490dc;
            margin-bottom: 5px;
            padding-left: 15px;
        }

        .tree-view .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .tree-view .form-check {
            padding-left: 25px;
        }

        .tree-view .form-check-input {
            position: absolute;
            left: 0;
            margin-top: 0.2rem;
        }

        .tree-view .toggle-btn {
            background: none;
            border: none;
            padding: 0;
            margin-left: 5px;
        }

        .tree-view .toggle-btn i {
            transition: transform 0.2s;
        }

        .tree-view .collapsed i.bi-chevron-down {
            transform: rotate(-90deg);
        }

        .productos-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
        }

        .sin-promociones {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-style: italic;
        }

        /* Estilos adicionales para el modal de edición */
        #editarPromocionModal .modal-content {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        #editarPromocionModal .modal-header {
            border-bottom: 1px solid #eee;
        }

        #editarPromocionModal .modal-footer {
            border-top: 1px solid #eee;
        }

        #editarPromocionModal .form-check-input:checked {
            background-color: #3490dc;
            border-color: #3490dc;
        }

        #editarPromocionModal .list-group-item {
            transition: all 0.3s ease;
        }

        #editarPromocionModal .list-group-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
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
                            <i class="fas fa-boxes me-2"></i>{{ __('Productos') }}
                        </span>

                        <div class="float-right d-flex gap-2">
                            <button class="btn btn-warning btn-sm" id="btnPromociones" data-bs-toggle="modal" data-bs-target="#promocionModal">
                                <i class="fas fa-tag me-1"></i> Promociones
                            </button>
                            <a href="{{ route('productos.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus-circle me-1"></i> {{ __('Nuevo Producto') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-check"></i> {{ $message }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover display responsive nowrap" width="100%" id="tblProducts">
                            <thead class="thead">
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Precio Compra</th>
                                    <th>Precio Venta</th>
                                    <th>Categoría</th>
                                    <th>Proveedor</th>
                                                <th>SKU</th>
            <th>¿Se puede devolver?</th>
                                    <th>Imagen</th>
                                    <th>Código Barras</th>
                                    <th>Promoción</th>
                                    <th>Acciones</th>
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

    <!-- Modal de Gestión de Promociones -->
    <div class="modal fade" id="promocionModal" tabindex="-1" aria-labelledby="promocionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="promocionModalLabel">
                        <i class="fas fa-tags me-2"></i>Gestión de Promociones
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Crear Nueva Promoción</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('promociones.guardar') }}" id="formPromocion" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="productos_seleccionados" id="productos_seleccionados">
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Promoción</label>
                                            <select name="tipo_promocion" class="form-select" required>
                                                <option value="" selected disabled>-- Seleccione --</option>
                                                <option value="2x1">2x1</option>
                                                <option value="3x2">3x2</option>
                                                <option value="50%">50% de descuento</option>
                                                <option value="Precio especial">Precio especial (15%)</option>
                                                <option value="Segunda unidad al 30%">Segunda unidad al 30%</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Fecha de finalización</label>
                                            <input type="date" name="fecha_fin" class="form-control" min="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Selección de Productos</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Proveedor</label>
                                                        <select name="id_proveedor" id="id_proveedor" class="form-select">
                                                            <option value="" selected disabled>-- Seleccione --</option>
                                                            @foreach($proveedores as $proveedor)
                                                                <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Categoría</label>
                                                        <select name="id_categoria" id="id_categoria" class="form-select" disabled>
                                                            <option value="" selected disabled>-- Seleccione proveedor --</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mt-3 productos-container" id="productosContainer" style="display: none;">
                                                    <h6>Productos seleccionables:</h6>
                                                    <div id="listaProductos">
                                                        <!-- Los productos se cargarán dinámicamente aquí -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check-circle me-2"></i>Aplicar Promoción
                                        </button>
                                        <button type="button" class="btn btn-promociones-vigentes" id="btnVerPromocionesVigentes">
                                            <i class="fas fa-list-check me-2"></i>Ver Promociones
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Promociones Vigentes -->
    <div class="modal fade" id="promocionesVigentesModal" tabindex="-1" aria-labelledby="promocionesVigentesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="promocionesVigentesModalLabel">
                        <i class="fas fa-tags me-2"></i>Promociones Vigentes
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card border-primary">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Listado de Promociones Activas</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="tblPromocionesVigentes">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-box me-1"></i>Producto</th>
                                            <th><i class="fas fa-folder me-1"></i>Categoría</th>
                                            <th><i class="fas fa-truck me-1"></i>Proveedor</th>
                                            <th><i class="fas fa-tag me-1"></i>Promoción</th>
                                            <th><i class="far fa-calendar-alt me-1"></i>Válida hasta</th>
                                            <th><i class="fas fa-cog me-1"></i>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $hayPromocionesActivas = false;
                                        @endphp

                                        @foreach($productosConPromocion as $producto)
                                            @php
                                                if($producto->promocion):
                                                    $fechaFin = \Carbon\Carbon::parse($producto->promocion->fecha_fin);
                                                    $estaActiva = !$fechaFin->isPast();

                                                    if($estaActiva):
                                                        $hayPromocionesActivas = true;
                                            @endphp
                                            <tr>
                                                <td class="align-middle">{{ $producto->producto }}</td>
                                                <td class="align-middle">{{ $producto->categoria->nombre ?? '-' }}</td>
                                                <td class="align-middle">{{ $producto->proveedor->nombre ?? '-' }}</td>
                                                            <td>{{ $producto->sku }}</td>
                                                            <td>
                                                                @if($producto->devolucion)
                                                                    <span class="badge bg-success">Sí</span>
                                                                @else
                                                                    <span class="badge bg-danger">No</span>
                                                                @endif
                                                            </td>
                                                <td class="align-middle">
                                                    @php
                                                        $badgeClass = '';
                                                        switch($producto->promocion->tipo) {
                                                            case '2x1': $badgeClass = 'badge-promo-2x1'; break;
                                                            case '3x2': $badgeClass = 'badge-promo-3x2'; break;
                                                            case '50%': $badgeClass = 'badge-promo-50'; break;
                                                            case 'Precio especial': $badgeClass = 'badge-promo-especial'; break;
                                                            case 'Segunda unidad al 30%': $badgeClass = 'badge-promo-30'; break;
                                                            default: $badgeClass = 'bg-secondary';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">
                                                        {{ $producto->promocion->tipo }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="fecha-promocion activa">
                                                        {{ $fechaFin->format('d/m/Y') }}
                                                    </span>
                                                    <span class="badge badge-activa">Activa</span>
                                                </td>
                                                <td class="align-middle">
                                                    <button class="btn btn-sm btn-primary btn-action btn-editar-promocion"
                                                            data-id="{{ $producto->id }}"
                                                            data-tipo="{{ $producto->promocion->tipo }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-action btn-eliminar-promocion"
                                                            data-id="{{ $producto->id }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @php
                                                    endif;
                                                endif;
                                            @endphp
                                        @endforeach

                                        @unless($hayPromocionesActivas)
                                            <tr>
                                                <td colspan="6" class="sin-promociones">
                                                    <i class="fas fa-info-circle me-2"></i> No hay promociones vigentes en este momento
                                                </td>
                                            </tr>
                                        @endunless
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edición de Promoción (Versión Completa) -->
    <div class="modal fade" id="editarPromocionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">

                <div class="modal-body" id="contenidoEditarPromocion">
                    <!-- Contenido se carga dinámicamente aquí -->

                </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="DataTables/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const table = new DataTable('#tblProducts', {
                responsive: true,
                fixedHeader: true,
                ajax: {
                    url: '{{ route("products.list") }}',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'id' },
                    { data: 'codigo' },
                    { data: 'producto' },
                    { data: 'precio_compra' },
                    { data: 'precio_venta' },
                    {
                        data: 'categoria',
                        render: function(data) {
                            return data ? data.nombre : '-';
                        }
                    },
                    {
                        data: 'proveedor',
                        render: function(data) {
                            return data ? data.nombre : '-';
                        }
                    },
                    {
                        data: 'sku',
                        render: function(data) {
                            return data !== null ? data : '-';
                        }
                    },
                    {
                        data: 'devolucion',
                        render: function(data) {
                            if (data == 1 || data === true) {
                                return '<span class="badge bg-success">Sí</span>';
                            }
                            return '<span class="badge bg-danger">No</span>';
                        }
                    },
                    {
                        data: 'foto',
                        render: function(data) {
                        return data ? `<img src="/storage/${data}" alt="Foto" style="max-width: 60px;">` : 'Sin imagen';
                        }
                    },
                    {
                        data: 'codigo_barras',
                        render: function(data, type, row) {
                            const barcodeId = `barcode-${row.id}`;
                            setTimeout(() => {
                                JsBarcode(`#${barcodeId}`, data, {
                                    format: "EAN13",
                                    width: 1.4,
                                    height: 40,
                                    displayValue: false
                                });
                            }, 100);
                            return `<svg id="${barcodeId}"></svg><small>${data}</small>`;
                        }
                    },
                    {
                        data: 'promocion',
                        render: function(data) {
                            if (data && data.tipo) {
                                const fechaFin = new Date(data.fecha_fin);
                                const hoy = new Date();
                                const estaActiva = fechaFin >= hoy;

                                if (estaActiva) {
                                    return `<span class="badge bg-warning text-dark">${data.tipo}</span>`;
                                } else {
                                    return '<span class="text-muted">Promoción expirada</span>';
                                }
                            }
                            return '<span class="text-muted">Sin promoción</span>';
                        }
                    },
                    {
                        data: null,
                        render: function(row) {
                            return `
                                <a href="/productos/${row.id}/edit" class="btn btn-sm btn-primary">Editar</a>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}">Eliminar</button>
                                <button class="btn btn-sm btn-info btn-editar-promocion"
                                        data-id="${row.id}"
                                        data-tipo="${row.promocion ? row.promocion.tipo : ''}">
                                    <i class="fas fa-tag"></i> Promo
                                </button>
                            `;
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                order: [[0, 'desc']]
            });

            $('#btnPromociones').on('click', function() {
                $('#promocionModal').modal('show');
            });

            $('#btnVerPromocionesVigentes').on('click', function() {
                $('#promocionModal').modal('hide');
                $('#promocionesVigentesModal').modal('show');
            });

            $('#promocionesVigentesModal').on('hidden.bs.modal', function () {
                $('#promocionModal').modal('show');
            });

            $('#formPromocion').submit(function(e) {
                e.preventDefault();

                const productosSeleccionados = $('.producto-check:checked').map(function() {
                    return $(this).val();
                }).get();

                if (productosSeleccionados.length === 0) {
                    Swal.fire('Error', 'Debes seleccionar al menos un producto', 'error');
                    return false;
                }

                $('#productos_seleccionados').val(JSON.stringify(productosSeleccionados));
                this.submit();
            });

            $(document).on('click', '.btn-editar-promocion', function(e) {
                e.preventDefault();
                const productoId = $(this).data('id');
                const tipoPromocion = $(this).data('tipo');

                $('#editarPromocionModal').modal('show');

                $.ajax({
                    url: `/productos/${productoId}/edit-promocion`,
                    method: 'GET',
                    success: function(response) {
                        $('#contenidoEditarPromocion').html(response);

                        if(tipoPromocion) {
                            $('#editarPromocionModal select[name="tipo_promocion"]').val(tipoPromocion);
                        }

                        $('#editarPromocionModal input[name="fecha_fin"]').attr('min', new Date().toISOString().split('T')[0]);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error al cargar el formulario. Por favor, intente nuevamente.';

                        if (xhr.status === 422 || xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        $('#contenidoEditarPromocion').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> ${errorMessage}
                            </div>
                        `);
                        console.error('Error al cargar formulario:', xhr.responseText);
                    }
                });
            });

            $(document).on('click', '#btnGuardarPromocion', function() {
                const form = $('#formEditarPromocion');
                if(form.length > 0) {
                    const formData = form.serialize();
                    const url = form.attr('action');

                    $.ajax({
                        url: url,
                        method: 'PUT',
                        data: formData,
                        beforeSend: function() {
                            $('#btnGuardarPromocion').prop('disabled', true).html(`
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Guardando...
                            `);
                        },
                        success: function(response) {
                            $('#editarPromocionModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: 'La promoción se ha actualizado correctamente',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error al actualizar la promoción',
                            });
                            console.error('Error al guardar:', xhr.responseText);
                        },
                        complete: function() {
                            $('#btnGuardarPromocion').prop('disabled', false).text('Guardar Cambios');
                        }
                    });
                }
            });

            $(document).on('click', '.btn-eliminar-promocion', function() {
                const productoId = $(this).data('id');

                Swal.fire({
                    title: '¿Eliminar promoción?',
                    text: "¿Estás seguro de quitar la promoción de este producto?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/productos/${productoId}/remove-promocion`,
                            method: 'DELETE',
                            success: function() {
                                Swal.fire(
                                    '¡Eliminada!',
                                    'La promoción ha sido removida.',
                                    'success'
                                ).then(() => {
                                    table.ajax.reload(null, false);
                                    $('#promocionesVigentesModal').modal('hide');
                                });
                            },
                            error: function() {
                                Swal.fire(
                                    'Error',
                                    'No se pudo eliminar la promoción',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const productId = $(this).data('id');

                Swal.fire({
                    icon: 'info',
                    title: 'Eliminar Producto',
                    text: '¿Estás seguro de que quieres eliminar este producto?',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/productos/${productId}`,
                            type: 'DELETE',
                            success: function() {
                                Swal.fire('¡Eliminado!', 'El producto ha sido eliminado.', 'success');
                                table.ajax.reload();
                            },
                            error: function() {
                                Swal.fire('Error', 'No se pudo eliminar el producto.', 'error');
                            }
                        });
                    }
                });
            });

            $('#id_proveedor').on('change', function() {
                const proveedorId = $(this).val();
                const $categoriaSelect = $('#id_categoria');

                $categoriaSelect.html('<option value="" selected disabled>-- Cargando --</option>').prop('disabled', true);

                if (!proveedorId) {
                    $('#productosContainer').hide();
                    return;
                }

                $.ajax({
                    url: `/promociones/categorias/${proveedorId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $categoriaSelect.empty();

                        if (response.success && response.data && response.data.length > 0) {
                            $categoriaSelect.append('<option value="" selected disabled>-- Seleccione --</option>');

                            response.data.forEach(categoria => {
                                $categoriaSelect.append(
                                    $(`<option value="${categoria.id}">${categoria.nombre}</option>`)
                                );
                            });

                            $categoriaSelect.prop('disabled', false);
                        } else {
                            const msg = response.message || 'No hay categorías disponibles';
                            $categoriaSelect.append(`<option value="" disabled selected>-- ${msg} --</option>`);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error en AJAX:', xhr.responseText);
                        $categoriaSelect.html(
                            '<option value="" disabled selected>-- Error al cargar --</option>'
                        );
                        Swal.fire('Error', 'No se pudieron cargar las categorías', 'error');
                    }
                });
            });

            $('#id_categoria').on('change', function() {
                const categoriaId = $(this).val();
                const $productosContainer = $('#productosContainer');
                const $listaProductos = $('#listaProductos');

                $listaProductos.empty();
                $productosContainer.hide();

                if (!categoriaId) return;

                $.ajax({
                    url: `/promociones/productos/${categoriaId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.length > 0) {
                            $.each(data, function(index, producto) {
                                $listaProductos.append(`
                                    <div class="form-check mb-2">
                                        <input class="form-check-input producto-check" type="checkbox"
                                               name="productos[]" value="${producto.id}" id="prod-${producto.id}">
                                        <label class="form-check-label" for="prod-${producto.id}">
                                            ${producto.producto}
                                        </label>
                                    </div>
                                `);
                            });
                            $productosContainer.show();
                        } else {
                            $listaProductos.html('<div class="alert alert-info">No hay productos en esta categoría</div>');
                            $productosContainer.show();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cargar productos:', error);
                        $listaProductos.html('<div class="alert alert-danger">Error al cargar productos</div>');
                        $productosContainer.show();
                    }
                });
            });

            function verificarPromocionesExpiradas() {
                $.ajax({
                    url: '{{ route("promociones.verificar") }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.eliminadas > 0) {
                            console.log(`Se eliminaron ${response.eliminadas} promociones expiradas`);
                            table.ajax.reload();
                        }
                    },
                    error: function() {
                        console.error('Error al verificar promociones expiradas');
                    }
                });
            }

            verificarPromocionesExpiradas();
        });
    </script>
@endsection
