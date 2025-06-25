    @extends('adminlte::page')

    @section('title', 'Dashboard')

    @section('content_header')
        <h1><i class="fas fa-users me-2"></i>Clientes Deudores</h1>
    @stop

    @section('css')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            #tblClients thead th {
                background-color: #3490dc;
                color: white;
                font-weight: 500;
            }
            #tblClients tbody tr:hover {
                background-color: rgba(52, 144, 220, 0.1);
            }
            .card-header {
                background-color: #3490dc !important;
                color: white;
            }
            .alert-success {
                border-left: 4px solid #28a745;
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
                    <div class="card-header">
                        <span id="card_title">
                            <i class="fas fa-user-clock me-2"></i>{{ __('Clientes Deudores') }}
                        </span>
                    </div>
                    <div class="card-body">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i><strong>{{ $message }}</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        <div class="table-responsive">
    <table id="tblClients" class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Total de la compra</th>
                <th>Días sin pagar</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>

    <!-- Modal método de pago para abono -->
    <div class="modal fade" id="modalAbono" tabindex="-1" aria-labelledby="modalAbonoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-primary">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalAbonoLabel">SELECCIONE MÉTODO DE ABONO</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body text-center">
            <h5 id="nombreCliente"></h5>
            <p>Total a abonar: <strong id="totalAbono"></strong></p>

            <div class="d-flex justify-content-around">
            <button id="btnAbonoEfectivo" class="btn btn-success"><i class="fas fa-money-bill-wave me-2"></i>EFECTIVO</button>
            <button id="btnAbonoTarjeta" class="btn btn-primary"><i class="fas fa-credit-card me-2"></i>TARJETA</button>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
        </div>
    </div>
    </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @stop

    @section('js')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    let clienteIdGlobal = null;
    let clienteNombreGlobal = '';
    let clienteDeudaGlobal = 0;

    $(document).ready(function () {
        const tabla = $('#tblClients').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("clientes.index") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'telefono', name: 'telefono' },
                { data: 'total_compra', name: 'total_compra' },
                { data: 'dias_sin_pagar', name: 'dias_sin_pagar', orderable: false, searchable: false },
                { data: 'acciones', name: 'acciones', orderable: false, searchable: false },
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-MX.json'
            }
        });

        // Abrir modal al presionar "Abonar"
        $(document).on('click', '.btn-abonar', function () {
            clienteIdGlobal = $(this).data('id');
            clienteNombreGlobal = $(this).data('nombre');
            clienteDeudaGlobal = parseFloat($(this).data('total'));

            $('#nombreCliente').text(clienteNombreGlobal);
            $('#totalAbono').text(`$${clienteDeudaGlobal.toFixed(2)}`);
            $('#modalAbono').modal('show');
        });

        // Método de abono: efectivo
        $('#btnAbonoEfectivo').off('click').on('click', function () {
            $('#modalAbono').modal('hide');
            setTimeout(() => {
                mostrarFormularioAbono('efectivo');
            }, 300);
        });

        // Método de abono: tarjeta
        $('#btnAbonoTarjeta').off('click').on('click', function () {
            $('#modalAbono').modal('hide');
            setTimeout(() => {
                mostrarFormularioAbono('tarjeta');
            }, 300);
        });
    });

    function mostrarFormularioAbono(metodo) {
        Swal.fire({
            title: `ABONO - ${metodo.toUpperCase()}`,
            html: `
                <p class="text-start">Cliente: <strong>${clienteNombreGlobal}</strong></p>
                <p class="text-start">Deuda actual: <strong>$${clienteDeudaGlobal.toFixed(2)}</strong></p>
                <label for="montoAbono">Monto a abonar:</label>
                <input type="number" id="montoAbono" class="form-control text-center" min="1" step="0.01">
            `,
            confirmButtonText: 'CONFIRMAR ABONO',
            showCancelButton: true,
            cancelButtonText: 'CANCELAR',
            preConfirm: () => {
                const monto = parseFloat(document.getElementById('montoAbono').value);
                if (isNaN(monto) || monto <= 0) {
                    Swal.showValidationMessage('Ingrese un monto válido');
                    return false;
                }
                return monto;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const monto = result.value;
                procesarAbono(clienteIdGlobal, metodo, monto);
            }
        });
    }

    function procesarAbono(clienteId, metodo, monto) {
        Swal.fire({
            title: 'PROCESANDO ABONO',
            html: `<div class="spinner-border text-primary" role="status"></div><br><br>Espere un momento...`,
            showConfirmButton: false,
            allowOutsideClick: false
        });

        fetch(`/clientes/${clienteId}/abonar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ metodo, monto })
        })
        .then(res => res.json())
    .then(data => {
        if (data.success) {
            const buttons = {
                confirmButtonText: 'Aceptar'
            };

            if (data.ticket_url) {
                buttons.showDenyButton = true;
                buttons.denyButtonText = 'Imprimir ticket';
            }

            Swal.fire({
                title: 'ABONO REGISTRADO',
                text: data.message,
                icon: 'success',
                ...buttons
            }).then((result) => {
                if (result.isDenied && data.ticket_url) {
                    window.open(data.ticket_url, '_blank');
                }
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'No se pudo registrar el abono', 'error');
        }
    })

        .catch(() => {
            Swal.fire('Error', 'Ocurrió un error al procesar el abono.', 'error');
        });
    }
    </script>
    @stop
