<div class="box box-info padding-1">
    <div class="box-body row">
        <div class="form-group col-md-4">
            {!! html()->label('Código (13 dígitos)')->for('codigo')->class('form-label') !!}
            <div class="input-group">
                {!! html()->text('codigo', $producto->codigo ?? old('codigo'))
                    ->class('form-control' . ($errors->has('codigo') ? ' is-invalid' : ''))
                    ->id('codigo')
                    ->attributes(['readonly' => 'readonly']) !!}
                <button class="btn btn-outline-secondary" type="button" id="btnGenerarCodigo">
                    <i class="fas fa-sync-alt"></i>
                </button>
                @error('codigo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group col-md-6">
            {!! html()->label('Nombre del Producto')->for('producto')->class('form-label') !!}
            {!! html()->text('producto', $producto->producto)
                ->class('form-control' . ($errors->has('producto') ? ' is-invalid' : ''))
                ->placeholder('Ingrese el nombre del producto') !!}
            @error('producto')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-md-2">
            {!! html()->label('Categoría')->for('id_categoria')->class('form-label') !!}
            <select name="id_categoria" id="id_categoria" class="form-control select-filtro">
                <option value="">-- Seleccione --</option>
                @foreach($categorias as $key => $value)
                    <option value="{{ $key }}" {{ $producto->id_categoria == $key ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
            @error('id_categoria')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-md-3">
            {!! html()->label('Precio Compra')->for('precio_compra')->class('form-label') !!}
            <div class="input-group">
                <span class="input-group-text">$</span>
                {!! html()->text('precio_compra', $producto->precio_compra)
                    ->class('form-control' . ($errors->has('precio_compra') ? ' is-invalid' : ''))
                    ->placeholder('0.00') !!}
                @error('precio_compra')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group col-md-3">
            {!! html()->label('Precio Venta')->for('precio_venta')->class('form-label') !!}
            <div class="input-group">
                <span class="input-group-text">$</span>
                {!! html()->text('precio_venta', $producto->precio_venta)
                    ->class('form-control' . ($errors->has('precio_venta') ? ' is-invalid' : ''))
                    ->placeholder('0.00') !!}
                @error('precio_venta')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group col-md-6">
            {!! html()->label('Proveedor')->for('proveedor_nombre')->class('form-label') !!}
            <div class="input-group">
                <input type="text" name="proveedor" id="proveedor_nombre" class="form-control" readonly>

            </div>
            <input type="hidden" id="id_proveedor" name="id_proveedor">
        </div>

        <div class="form-group col-md-6">
            {!! html()->label('Código de Barras (EAN-13)')->for('codigo_barras')->class('form-label') !!}
            <div class="input-group">
                {!! html()->text('codigo_barras', $producto->codigo_barras ?? old('codigo_barras'))
                    ->class('form-control' . ($errors->has('codigo_barras') ? ' is-invalid' : ''))
                    ->id('codigo_barras')
                    ->attributes(['readonly' => 'readonly']) !!}
                <button class="btn btn-outline-secondary" type="button" id="btnGenerarBarras">
                    <i class="fas fa-barcode"></i>
                </button>
                @error('codigo_barras')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-3 text-center" id="barcode-container">
                @if(!empty($producto->codigo_barras))
                    <svg id="barcode-svg" class="border p-2 bg-white rounded"></svg>
                    <small class="text-muted d-block mt-1">{{ $producto->codigo_barras }}</small>
                @else
                    <div class="alert alert-info p-2 mb-0">El código de barras se generará automáticamente</div>
                @endif
            </div>
        </div>

        <div class="form-group col-md-6">
            {!! html()->label('Imagen del Producto')->for('foto')->class('form-label') !!}
            <div class="file-upload-wrapper border rounded p-3 text-center">
                {!! html()->file('foto')
                    ->class('form-control visually-hidden' . ($errors->has('foto') ? ' is-invalid' : ''))
                    ->id('fotoInput') !!}

                <div id="imagePreview" class="mb-3">
                    @if(isset($producto) && $producto->foto)
                        <img src="{{ asset('storage/'.$producto->foto) }}" alt="Imagen actual" class="img-thumbnail" style="max-height: 200px;">
                    @else
                        <div class="placeholder-image bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <div class="text-center">
                                <i class="fas fa-image fa-4x text-muted mb-2"></i>
                                <p class="text-muted">No hay imagen seleccionada</p>
                            </div>
                        </div>
                    @endif
                </div>

                <label for="fotoInput" class="btn btn-outline-primary">
                    <i class="fas fa-upload me-2"></i>Seleccionar Imagen
                </label>
                @error('foto')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="box-footer mt-4 text-right">
        {!! html()->a('/productos', __('Cancelar'))->class('btn btn-danger me-2') !!}
        {!! html()->button(__('Guardar Producto'))->type('submit')->class('btn btn-primary') !!}
    </div>
</div>

<style>
    .form-label {
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .select-filtro {
        border-radius: 5px;
        border: 1px solid #ddd;
        transition: all 0.3s;
    }

    .select-filtro:focus {
        border-color: #3490dc;
        box-shadow: 0 0 0 0.25rem rgba(52, 144, 220, 0.25);
    }

    .file-upload-wrapper {
        transition: all 0.3s;
        background-color: #f8f9fa;
    }

    .file-upload-wrapper:hover {
        background-color: #e9ecef;
    }

    .placeholder-image {
        border: 2px dashed #dee2e6;
        border-radius: 5px;
    }

    #barcode-container {
        background-color: white;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .input-group-text {
        background-color: #f8f9fa;
    }
</style>
@section('js')
<!-- Cargar la librería JsBarcode -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("Script de código de barras cargado");

    const categoriaSelect = document.getElementById('id_categoria');
    const barcodeContainer = document.getElementById('barcode-container');
    const codigoBarrasInput = document.getElementById('codigo_barras');

    function formatEAN13(value) {
        if (!value) return null;
        value = value.toString().replace(/\D/g, '').substring(0, 13).padStart(13, '0');
        if (value.length === 13) {
            const digits = value.split('').map(Number);
            const checkDigit = digits.pop();
            let sum = 0;
            digits.forEach((digit, index) => {
                sum += digit * (index % 2 === 0 ? 1 : 3);
            });
            const calculatedCheck = (10 - (sum % 10)) % 10;
            if (checkDigit !== calculatedCheck) {
                return value.substring(0, 12) + calculatedCheck;
            }
        }
        return value;
    }

    function generateBarcode(value) {
        try {
            const formattedValue = formatEAN13(value);
            if (!formattedValue) throw new Error("Código inválido");

            codigoBarrasInput.value = formattedValue;
            barcodeContainer.innerHTML = '<svg id="barcode-svg"></svg>';
            JsBarcode("#barcode-svg", formattedValue, {
                format: "EAN13",
                lineColor: "#000",
                width: 2,
                height: 60,
                displayValue: true,
                fontSize: 14,
                margin: 5
            });
        } catch (error) {
            barcodeContainer.innerHTML = `
                <div class="alert alert-danger p-2">
                    Error: ${error.message}<br>Valor: ${value || 'Ninguno'}
                </div>`;
        }
    }

    async function loadCategoryData(categoryId) {
        try {
            barcodeContainer.innerHTML = '<div class="alert alert-info p-2">Generando código...</div>';
            const response = await fetch(`/api/categoria-info/${categoryId}`);
            const data = await response.json();

            if (!data.success) throw new Error(data.error || 'Error en datos');

            document.getElementById('codigo').value = data.data.codigo || '';
            document.getElementById('codigo_barras').value = data.data.codigo_barras || '';
            document.getElementById('proveedor_nombre').value = data.data.proveedor?.nombre || '';
            document.getElementById('id_proveedor').value = data.data.proveedor?.id || '';

            if (data.data.codigo_barras) {
                generateBarcode(data.data.codigo_barras);
            }

        } catch (error) {
            barcodeContainer.innerHTML = `<div class="alert alert-danger p-2">${error.message}</div>`;
        }
    }

    if (categoriaSelect) {
        categoriaSelect.addEventListener('change', function() {
            if (this.value) {
                loadCategoryData(this.value);
            } else {
                barcodeContainer.innerHTML = '<div class="alert alert-info p-2">Seleccione una categoría</div>';
                if (codigoBarrasInput) codigoBarrasInput.value = '';
            }
        });

        if (categoriaSelect.value) {
            loadCategoryData(categoriaSelect.value);
        }
    }

    // Intentar generar el código si ya viene un valor desde el backend
    if (codigoBarrasInput.value && codigoBarrasInput.value.length === 13) {
        generateBarcode(codigoBarrasInput.value);
    }
});
</script>
@endsection

