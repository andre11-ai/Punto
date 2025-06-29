<div>
    <div class="row">
    <!-- Lista de productos -->
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-primary text-white">
                        <i class="fas fa-search"></i>
                    </span>
                    <input wire:model.debounce.500ms="search" type="text"
                           placeholder="Buscar por nombre o código de barras..."
                           class="form-control">
                </div>

                @if($products->isEmpty())
                    <div class="alert alert-warning text-center py-4">
                        <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                        <p class="mb-0">No se encontraron productos</p>
                    </div>
                @else
                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
@foreach ($products as $product)
    <div class="col">
        <div class="card h-100 border-0 shadow-sm product-card
            {{ $product->sku <= 0 ? 'bg-danger bg-opacity-10 border-danger' : '' }}">
            <div class="card-body text-center p-2">
                @if($product->foto)
                    <img src="{{ asset('storage/'.$product->foto) }}"
                         class="img-fluid mb-2 rounded"
                         style="max-height: 100px; object-fit: contain;">
                @else
                    <img src="{{ asset('img/default.png') }}"
                         class="img-fluid mb-2 rounded"
                         style="max-height: 100px; object-fit: contain;">
                @endif

<div class="d-flex justify-content-center align-items-center mb-2">
    <button class="btn btn-outline-secondary btn-sm py-0 px-2"
            wire:click="decreaseQuantity({{ $product->id }})"
            type="button"
            {{ $product->sku <= 0 ? 'disabled' : '' }}>
        <i class="fas fa-minus"></i>
    </button>
    <input type="number" readonly
           class="form-control form-control-sm text-center mx-1"
           style="max-width: 40px;"
           min="1"
           max="{{ $product->sku }}"
           value="{{ $quantitySelector[$product->id] ?? 1 }}">
    <button class="btn btn-outline-secondary btn-sm py-0 px-2"
            wire:click="increaseQuantity({{ $product->id }})"
            type="button"
            {{ $product->sku <= 0 || ($quantitySelector[$product->id] ?? 1) >= $product->sku ? 'disabled' : '' }}>
        <i class="fas fa-plus"></i>
    </button>
</div>

<button wire:click="addToCart({{ $product->id }})"
        wire:loading.attr="disabled"
        wire:target="addToCart"
        class="btn {{ $product->sku <= 0 ? 'btn-danger' : 'btn-primary' }} btn-sm w-100 mb-2"
        {{ $product->sku <= 0 ? 'disabled' : '' }}>
    <i class="fas fa-cart-plus"></i>
    {{ $product->sku <= 0 ? 'Agotado' : 'Agregar' }}
</button>

                <h6 class="card-title mb-1 text-truncate">
                    {{ $product->producto }}
                </h6>

                <p class="mb-1 text-muted small">
                    <small>{{ $product->codigo_barras }}</small>
                </p>

                <p class="mb-0 fw-bold {{ $product->sku <= 0 ? 'text-danger' : 'text-primary' }}">
                    ${{ number_format($product->precio_venta, 2) }}
                </p>
                <p class="mb-0">
                    @if($product->sku > 0)
                        <span class="badge bg-success">Stock: {{ $product->sku }}</span>
                    @else
                        <span class="badge bg-danger">AGOTADO</span>
                    @endif
                </p>
                @if($product->promocion)
                    <span class="badge bg-warning text-dark mt-1">
                        {{ $product->promocion->tipo }}
                    </span>
                @endif
            </div>
        </div>
    </div>
@endforeach
                    </div>

                    <div class="mt-3 d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Carrito de compras -->
    <div class="col-md-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Carrito
                    </h5>
                    <span class="badge bg-white text-primary">
                        {{ count($cartItems) }} items
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                @php $total = 0; $ahorro = 0; @endphp

                @if(count($cartItems) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40%">Producto</th>
                                    <th class="text-end">Precio</th>
                                    @if($product->sku > 0)
    <span class="badge bg-success mb-1">Stock: {{ $product->sku }}</span>
@else
    <span class="badge bg-danger mb-1">AGOTADO</span>
@endif
                                    <th class="text-center">Cant.</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                    @php
                                        $promo = strtolower($item['options']['promocion'] ?? '');
                                        $original = $item['price'];
                                        $qty = $item['qty'];
                                        $subtotal = $original * $qty;
                                        $subtotalFinal = $subtotal;
                                        $label = null;

                                        if ($promo) {
                                            switch ($promo) {
                                                case '2x1':
                                                    if ($qty > 1) {
                                                        $subtotalFinal = ceil($qty / 2) * $original;
                                                        $label = '2x1';
                                                    }
                                                    break;
                                                case '3x2':
                                                    if ($qty > 1) {
                                                        $sets = floor($qty / 3);
                                                        $resto = $qty % 3;
                                                        $subtotalFinal = ($sets * 2 + $resto) * $original;
                                                        $label = '3x2';
                                                    }
                                                    break;
                                                case '50%':
                                                case '50% de descuento':
                                                    $subtotalFinal = $subtotal * 0.5;
                                                    $label = '50%';
                                                    break;
                                                case 'precio especial':
                                                    $subtotalFinal = $subtotal * 0.85;
                                                    $label = '15%';
                                                    break;
                                                case 'segunda unidad al 30%':
                                                    if ($qty > 1) {
                                                        $pares = floor($qty / 2);
                                                        $impares = $qty % 2;
                                                        $subtotalFinal = ($pares * ($original + ($original * 0.3))) + ($impares * $original);
                                                        $label = '2da -30%';
                                                    }
                                                    break;
                                            }
                                        }

                                        $total += $subtotalFinal;
                                        $ahorro += ($subtotal - $subtotalFinal);
                                    @endphp

                                    <tr wire:key="cart-item-{{ $item['rowId'] }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item['options']['foto'] ?? false)
                                                    <img src="{{ asset('storage/'.$item['options']['foto']) }}"
                                                         class="rounded me-2"
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('img/default.png') }}"
                                                         class="rounded me-2"
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $item['name'] }}</div>
                                                    <small class="text-muted">{{ $item['options']['codigo_barras'] ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">${{ number_format($item['price'], 2) }}</td>
                                        <td class="text-center">
                                            <input type="number"
                                                   wire:model="quantity.{{ $item['rowId'] }}"
                                                   wire:change.debounce.500ms="updateQuantity('{{ $item['rowId'] }}')"
                                                   min="1"
                                                   class="form-control form-control-sm text-center"
                                                   style="width: 60px; display: inline-block;"
                                                   value="{{ $item['qty'] }}">
                                        </td>
                                        <td class="text-end">
                                            ${{ number_format($subtotalFinal, 2) }}
                                            @if($label)
                                                <div><span class="badge bg-info text-dark">{{ $label }}</span></div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button wire:click="removeFromCart('{{ $item['rowId'] }}')"
                                                    class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Subtotal:</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                        @if($ahorro > 0)
                            <div class="d-flex justify-content-between align-items-center mb-2 text-success">
                                <span class="fw-bold">Usted Ahorro:</span>
                                <span>-${{ number_format($ahorro, 2) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold fs-5">Total:</span>
                            <span class="fw-bold fs-5">${{ number_format($total, 2) }}</span>
                        </div>

                        <div class="d-grid gap-2">
                            <button wire:click="clearCart"
                                    class="btn btn-outline-danger"
                                    @if(count($cartItems) == 0) disabled @endif>
                                <i class="fas fa-trash me-2"></i>Vaciar carrito
                            </button>
                            <button id="btnPagar"
                                    class="btn btn-primary btn-lg"
                                    onclick="mostrarMetodosPago()"
                                    @if(count($cartItems) == 0) disabled @endif>
                                <i class="fas fa-cash-register me-2"></i>Pagar
                            </button>

                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">El carrito está vacío</h5>
                        <p class="text-muted small">Agrega productos para comenzar</p>
                    </div>
                @endif
            </div>
        </div>
    </div>


</div>

    <div wire:ignore.self class="modal fade" id="adeudoModal" tabindex="-1" aria-labelledby="adeudoModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-warning">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="adeudoModalLabel">Registrar Deudor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formDeudor">
                        <div class="mb-3">
                            <label for="nombreDeudor" class="form-label">Nombre completo</label>
                            <input type="text" id="nombreDeudor" name="nombre" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="telefonoDeudor" class="form-label">Teléfono</label>
                            <input type="text" id="telefonoDeudor" name="telefono" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="totalAdeudo" class="form-label">Total de la compra</label>
                            <input type="text" class="form-control" id="totalAdeudo" readonly>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" id="btnGuardarDeudor">Guardar Deudor</button>
                </div>
            </div>
        </div>
    </div>


</div>




@push('styles')
<style>
    /* --------- ESTILO GENERAL DE TARJETAS Y BOTONES --------- */
.card-header.bg-primary, .card-header {
    background-color: #3490dc !important;
    color: white !important;
}
.btn-primary, .btn-primary:disabled, .btn-primary:focus {
    background-color: #3490dc !important;
    border-color: #2a7aaf !important;
}
.btn-primary:hover {
    background-color: #2a7aaf !important;
    border-color: #226089 !important;
}
.btn-outline-danger:hover, .btn-outline-danger:focus {
    background-color: #dc3545 !important;
    color: #fff !important;
    border-color: #d52d3a !important;
}

.badge.bg-primary, .badge.bg-info, .badge.bg-warning, .badge.bg-white, .badge.bg-success {
    font-weight: 500;
    font-size: 0.85rem;
    border-radius: 0.4rem;
    padding: 0.38em 0.8em;
}
.badge.bg-primary { background-color: #3490dc !important; color: #fff !important; }
.badge.bg-info { background-color: #e3e7f3 !important; color: #2a7aaf !important; }
.badge.bg-warning { background-color: #ffe066 !important; color: #856404 !important; }
.badge.bg-white { background-color: #fff !important; color: #3490dc !important; }
.badge.bg-success { background-color: #28a745 !important; color: #fff !important; }

.card.shadow-sm {
    box-shadow: 0 2px 10px rgba(52, 144, 220, 0.08), 0 1.5px 3px rgba(0,0,0,0.03);
    border: none;
}
.product-card {
    border: 1px solid #e9ecef;
    transition: all 0.3s;
}
.product-card:hover {
    box-shadow: 0 6px 24px rgba(52, 144, 220, 0.14);
    border-color: #3490dc;
    transform: translateY(-5px) scale(1.01);
}
.btn-action {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    border-radius: 4px;
}

/* --------- ALERTAS --------- */
.alert-success {
    border-left: 4px solid #28a745;
}
.alert-success strong {
    color: #28a745;
}
.alert-warning {
    border-left: 4px solid #ffe066;
}
.alert-warning i {
    color: #ffe066;
}
.alert-warning {
    color: #856404;
    background-color: #fffbe6;
    border-color: #ffe066;
}

/* --------- MODALES --------- */
.modal-header.bg-warning {
    background-color: #ffe066 !important;
    color: #856404 !important;
}

.btn-close {
    filter: invert(1);
}

/* --------- TABLA DEL CARRITO --------- */
.table thead th, .table-light th {
    background-color: #3490dc !important;
    color: #fff !important;
    font-weight: 500;
}
.table > tbody > tr > td {
    vertical-align: middle;
}
.table-hover > tbody > tr:hover {
    background-color: rgba(52, 144, 220, 0.08);
}

/* --------- OTROS DETALLES --------- */
input.form-control:read-only, input.form-control[readonly] {
    background-color: #f0f7ff !important;
    color: #3490dc !important;
    font-weight: bold;
}
@media (max-width: 650px) {
    .card-header h5, .card-header span, .card-header .badge { font-size: 1.1rem !important; }
}
    .product-card {
        transition: all 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .swal2-popup {
        border-radius: 10px !important;
    }

    .swal2-title {
        font-size: 1.5rem !important;
        color: #333 !important;
    }

    .payment-method-card {
        border: 2px solid #eee;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-method-card:hover {
        border-color: #4e73df;
        background-color: #f8f9fa;
    }

    .payment-method-card.selected {
        border-color: #4e73df;
        background-color: #f0f7ff;
    }

    .payment-method-icon {
        font-size: 2rem;
        margin-bottom: 10px;
        color: #4e73df;
    }

    .cash-input {
        font-size: 1.5rem !important;
        font-weight: bold !important;
        text-align: center !important;
        padding: 15px !important;
    }

    .change-message {
        font-size: 1.2rem;
        font-weight: bold;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
    }

    .change-positive {
        background-color: #d4edda;
        color: #155724;
    }

    .change-negative {
        background-color: #f8d7da;
        color: #721c24;
    }
    .modal-backdrop.show {
    z-index: 1050;
}

.modal.fade.show {
    z-index: 1060;
}
</style>
@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
    function mostrarMetodosPago() {
        @this.call('getTotalConPromociones').then(total => {
            Swal.fire({
                title: 'SELECCIONE MÉTODO DE PAGO',
                html: `
                    <div class="row g-3">
                        <div class="col-md-4">
                            <button class="btn btn-success w-100 py-3" id="swalEfectivo">
                                <i class="fas fa-money-bill-wave fa-lg me-2"></i>
                                <span class="fw-bold">EFECTIVO</span>
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary w-100 py-3" id="swalTarjeta">
                                <i class="fas fa-credit-card fa-lg me-2"></i>
                                <span class="fw-bold">TARJETA</span>
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-warning w-100 py-3" id="swalAdeudo">
                                <i class="fas fa-user-clock fa-lg me-2"></i>
                                <span class="fw-bold">ADEUDO</span>
                            </button>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                allowOutsideClick: true,
                backdrop: false,
                width: '600px',
                didOpen: () => {
                    document.getElementById('swalEfectivo').addEventListener('click', function () {
                        Swal.close();
                        simularCobroEfectivo(total);
                    });

                    document.getElementById('swalTarjeta').addEventListener('click', function () {
                        Swal.close();
                        simularCobroTarjeta(total);
                    });

                    document.getElementById('swalAdeudo').addEventListener('click', function () {
                        abrirModalDeudor(total);
                    });
                }
            });
        });
    }

 function abrirModalDeudor(total) {
        const totalInput = document.getElementById('totalAdeudo');
        const modalElement = document.getElementById('adeudoModal');

        if (!totalInput || !modalElement) return;

        totalInput.value = parseFloat(total).toFixed(2);

        Swal.close();


        setTimeout(() => {
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }, 300);
    }



document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('btnGuardarDeudor').addEventListener('click', () => {
        const nombre = document.getElementById('nombreDeudor').value.trim();
        const telefono = document.getElementById('telefonoDeudor').value.trim();

        if (!nombre || !telefono) {
            alert("Por favor, completa todos los campos.");
            return;
        }


        const modal = bootstrap.Modal.getInstance(document.getElementById('adeudoModal'));
        if (modal) modal.hide();

        @this.call('procesarVenta', 'adeudo', {
            nombre: nombre,
            telefono: telefono
        }).then(response => {
            if (response.success && response.cliente_id) {
                Swal.fire({
                    icon: 'success',
                    title: '¡DEUDA REGISTRADA!',
                    html: `
                        <p>Ahora puedes generar el ticket de abono.</p>
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button id="btnAceptar" class="btn btn-secondary">Aceptar</button>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        document.getElementById('btnAceptar').addEventListener('click', () => {
                            Swal.close();
                            location.reload();
                        });
                    }
                });
            } else {
                Swal.fire('Error', response.message || 'No se pudo registrar la deuda.', 'error');
            }
        }).catch(() => {
            Swal.fire('Error', 'Ocurrió un problema al guardar al deudor.', 'error');
        });
    });
});
    function simularCobroEfectivo(total) {
        Swal.fire({
            title: 'PAGO EN EFECTIVO',
            html: `
                <div class="text-center mb-3">
                    <h4 class="fw-bold">Total a pagar: $${parseFloat(total).toFixed(2)}</h4>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Ingrese el monto recibido:</label>
                    <input id="efectivo-recibido"
                           class="form-control form-control-lg text-center fw-bold cash-input"
                           type="number"
                           min="${total}"
                           step="0.01"
                           placeholder="0.00"
                           autofocus>
                </div>
                <div id="cambio-mensaje" class="change-message"></div>
            `,
            confirmButtonText: 'CONFIRMAR PAGO',
            showCancelButton: true,
            cancelButtonText: 'CANCELAR',
            preConfirm: () => {
                const recibido = parseFloat(document.getElementById('efectivo-recibido').value);
                if (isNaN(recibido) || recibido < total) {
                    Swal.showValidationMessage(`El monto debe ser mayor o igual a $${parseFloat(total).toFixed(2)}`);
                    return false;
                }
                return recibido;
            },
            didOpen: () => {
                const input = document.getElementById('efectivo-recibido');
                const mensaje = document.getElementById('cambio-mensaje');

                input.addEventListener('input', function () {
                    const recibido = parseFloat(this.value) || 0;
                    const cambio = recibido - total;

                    if (recibido >= total) {
                        mensaje.innerHTML = `Cambio: $${cambio.toFixed(2)}`;
                        mensaje.className = 'change-message change-positive';
                    } else {
                        mensaje.innerHTML = `Faltan: $${(total - recibido).toFixed(2)}`;
                        mensaje.className = 'change-message change-negative';
                    }
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const montoRecibido = result.value;
                procesarPago('efectivo', montoRecibido);
            }
        });
    }

    function simularCobroTarjeta(total) {
        Swal.fire({
            title: 'PAGO CON TARJETA',
            html: `
                <div class="text-center mb-3">
                    <h4 class="fw-bold">Total a pagar: $${parseFloat(total).toFixed(2)}</h4>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    ¿Desea procesar el pago con tarjeta por $${parseFloat(total).toFixed(2)}?
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'SI, PROCESAR PAGO',
            cancelButtonText: 'NO, CANCELAR',
        }).then((result) => {
            if (result.isConfirmed) {
                procesarPago('tarjeta', total);
            }
        });
    }

    function procesarPago(metodo, totalRecibido) {
        Swal.fire({
            title: 'PROCESANDO PAGO',
            html: `<div class="spinner-border text-primary" role="status"></div><br><br>Espere un momento...`,
            showConfirmButton: false,
            allowOutsideClick: false
        });

        @this.call('procesarVenta', metodo, totalRecibido)
            .then(response => {
                if (response.success) {
                    Swal.fire({
                        title: '¡PAGO EXITOSO!',
                        icon: 'success',
                        html: `Total: $${parseFloat(response.total).toFixed(2)}<br>Método: ${metodo.toUpperCase()}`,
                        confirmButtonText: 'IMPRIMIR TICKET',
                        showCancelButton: true,
                        cancelButtonText: 'FINALIZAR'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(`/venta/${response.ticket}/ticket`, '_blank');
                        }
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'No se pudo procesar la venta.', 'error');
            });
    }
</script>
@endpush


