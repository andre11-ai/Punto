<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">
            <i class="fas fa-truck me-2"></i>
            {{ isset($proveedor->id) ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
        </h4>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <!-- Campo Nombre -->
                <div class="form-group mb-4">
                    {!! html()->label('Nombre del Proveedor', 'nombre')
                        ->class('form-label fw-bold text-primary') !!}
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-building text-primary"></i>
                        </span>
                        {!! html()->text('nombre', $proveedor->nombre ?? '')
                            ->class('form-control' . ($errors->has('nombre') ? ' is-invalid' : ''))
                            ->placeholder('Ej: Distribuidora ABC, Proveedor XYZ...')
                            ->attributes(['autofocus' => 'autofocus']) !!}
                    </div>
                    @error('nombre')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Campo UPC -->
                <div class="form-group mb-4">
                    {!! html()->label('Código UPC', 'upc')
                        ->class('form-label fw-bold text-primary') !!}
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-barcode text-primary"></i>
                        </span>
                        {!! html()->text('upc', $proveedor->upc ?? '')
                            ->class('form-control' . ($errors->has('upc') ? ' is-invalid' : ''))
                            ->placeholder('Ej: 123456789012')
                            ->maxlength(12) !!}
                    </div>
                    @error('upc')
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
            <a href="{{ route('proveedores.index') }}" class="btn btn-danger">
                <i class="fas fa-times-circle me-2"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>
                {{ isset($proveedor->id) ? 'Actualizar Proveedor' : 'Guardar Proveedor' }}
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
        min-width: 45px;
        justify-content: center;
    }

    .form-control:focus {
        border-color: #3490dc;
        box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
    }

    .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        min-width: 120px;
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

    .input-group:hover .input-group-text {
        background-color: #e9f5ff;
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // Efecto hover en los inputs
        $('.form-control').hover(
            function() {
                $(this).css('border-color', '#94c6f0');
                $(this).prev('.input-group-text').css('background-color', '#e9f5ff');
            },
            function() {
                $(this).css('border-color', '#ced4da');
                $(this).prev('.input-group-text').css('background-color', '#f8f9fa');
            }
        );

        // Auto-focus en el primer campo
        $('input[name="nombre"]').focus();
    });
</script>
@endpush
