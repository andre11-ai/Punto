<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">
            <i class="fas fa-folder me-2"></i>
            {{ isset($categoria->id) ? 'Editar Categoría' : 'Nueva Categoría' }}
        </h4>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="form-group mb-4">
                    {!! html()->label('Nombre de la Categoría', 'nombre')
                        ->class('form-label fw-bold text-primary') !!}
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-tag text-primary"></i>
                        </span>
                        {!! html()->text('nombre', $categoria->nombre)
                            ->class('form-control' . ($errors->has('nombre') ? ' is-invalid' : ''))
                            ->placeholder('Ej. Electrónicos, Ropa, Alimentos...')
                            ->attributes(['autofocus' => 'autofocus']) !!}
                    </div>
                    @error('nombre')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    {!! html()->label('Proveedor', 'proveedor_id')
                        ->class('form-label fw-bold text-primary') !!}
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-truck text-primary"></i>
                        </span>
                        <select name="proveedor_id" class="form-select select2" id="proveedor_id">
                            <option value="">-- Seleccionar proveedor --</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}"
                                    {{ (old('proveedor_id', $categoria->proveedor_id ?? '') == $proveedor->id) ? 'selected' : '' }}>
                                    {{ $proveedor->nombre }} - {{ $proveedor->upc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('proveedor_id')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer bg-light">
        <div class="d-flex justify-content-between">
            <a href="{{ route('categorias.index') }}" class="btn btn-danger">
                <i class="fas fa-times-circle me-2"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>
                {{ isset($categoria->id) ? 'Actualizar Categoría' : 'Guardar Categoría' }}
            </button>
        </div>
    </div>
</div>

@push('css')
<style>
    .card {
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .form-label {
        margin-bottom: 0.5rem;
        display: block;
    }

    .input-group-text {
        transition: all 0.3s ease;
    }

    .form-control:focus, .select2:focus {
        border-color: #3490dc;
        box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
    }

    .select2 {
        width: 100% !important;
    }

    .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(52, 144, 220, 0.3);
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    .invalid-feedback {
        margin-top: 0.5rem;
        font-size: 0.85rem;
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // Inicializar Select2 si está disponible
        if($.fn.select2) {
            $('.select2').select2({
                placeholder: 'Seleccione un proveedor',
                allowClear: true
            });
        }

        // Efecto hover en los inputs
        $('.form-control, .select2-selection').hover(
            function() {
                $(this).css('border-color', '#94c6f0');
            },
            function() {
                $(this).css('border-color', '#ced4da');
            }
        );
    });
</script>
@endpush
