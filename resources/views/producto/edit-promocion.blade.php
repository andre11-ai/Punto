<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Editar Promoción</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        @if(!$producto->promocion)
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i> Este producto no tiene una promoción asociada.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        @else
            <form method="POST" action="{{ route('productos.update-promocion', $producto->id) }}" id="formEditarPromocion">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Columna izquierda - Información de la promoción -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de la Promoción</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Producto Actual:</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-box"></i></span>
                                            <input type="text" class="form-control" value="{{ $producto->producto }}" readonly>
                                            <input type="hidden" name="producto_actual" value="{{ $producto->id }}">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Aplicada por:</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                            <input type="text" class="form-control"
                                                value="@if($producto->promocion->id_categoria)
                                                    Categoría: {{ $producto->categoria->nombre }}
                                                @elseif($producto->promocion->id_proveedor)
                                                    Proveedor: {{ $producto->proveedor->nombre }}
                                                @else
                                                    Producto individual
                                                @endif" readonly>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tipo de Promoción</label>
                                        <select name="tipo_promocion" class="form-select" required>
                                            <option value="" selected disabled>-- Seleccione --</option>
                                            <option value="2x1" {{ $producto->promocion->tipo == '2x1' ? 'selected' : '' }}>2x1</option>
                                            <option value="3x2" {{ $producto->promocion->tipo == '3x2' ? 'selected' : '' }}>3x2</option>
                                            <option value="50%" {{ $producto->promocion->tipo == '50%' ? 'selected' : '' }}>50% de descuento</option>
                                            <option value="Precio especial" {{ $producto->promocion->tipo == 'Precio especial' ? 'selected' : '' }}>Precio especial</option>
                                            <option value="Segunda unidad al 30%" {{ $producto->promocion->tipo == 'Segunda unidad al 30%' ? 'selected' : '' }}>Segunda unidad al 30%</option>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Fecha de inicio</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                    <input type="date" name="fecha_inicio" class="form-control"
                                                           value="{{ $producto->promocion->fecha_inicio->format('Y-m-d') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Fecha de finalización</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                    <input type="date" name="fecha_fin" class="form-control"
                                                           value="{{ $producto->promocion->fecha_fin->format('Y-m-d') }}"
                                                           min="{{ date('Y-m-d') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha - Productos con esta promoción -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Productos con esta promoción</h6>
                                    <span class="badge bg-primary rounded-pill">{{ $productosRelacionados->count() }}</span>
                                </div>
                                <div class="card-body p-0">
                                    @if($productosRelacionados->count() > 0)
                                        <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                                            @foreach($productosRelacionados as $prod)
                                                <div class="list-group-item border-0 py-2 px-3">
                                                    <div class="form-check d-flex align-items-center">
                                                        <input class="form-check-input me-3" type="checkbox"
                                                               name="productos[]"
                                                               value="{{ $prod->id }}"
                                                               id="prod_{{ $prod->id }}"
                                                               {{ $prod->promocion_id == $producto->promocion_id ? 'checked' : '' }}
                                                               {{ $prod->id == $producto->id ? 'disabled' : '' }}>
                                                        <div class="w-100">
                                                            <label class="form-check-label d-flex justify-content-between align-items-center" for="prod_{{ $prod->id }}">
                                                                <span>{{ $prod->producto }}</span>
                                                                @if($prod->id == $producto->id)
                                                                    <span class="badge bg-info ms-2">Actual</span>
                                                                @endif
                                                            </label>
                                                            <div class="d-flex justify-content-between mt-1">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-tag me-1"></i>
                                                                    {{ $prod->categoria->nombre ?? 'Sin categoría' }}
                                                                </small>
                                                                <small class="{{ $prod->promocion_id ? 'text-warning' : 'text-muted' }}">
                                                                    <i class="fas fa-percentage me-1"></i>
                                                                    {{ $prod->promocion_id ? $prod->promocion->tipo : 'Sin promoción' }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No hay otros productos relacionados</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
