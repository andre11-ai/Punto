@extends('adminlte::page')

@section('title', 'Configuración de Compañía')

@section('content_header')
    <h1><i class="fas fa-building me-2"></i>Configuración de Compañía</h1>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        .form-control:focus {
            border-color: #3490dc;
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }
        .invalid-feedback {
            display: block;
            color: #dc3545;
        }
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <span id="card_title">
                        <i class="fas fa-cog me-2"></i>{{ __('Configuración de la Compañía') }}
                    </span>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><strong>{{ $message }}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('compania.update', $compania) }}" class="needs-validation" novalidate>
                        @method('PUT')
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" id="nombre" name="nombre"
                                        class="form-control{{ $errors->has('nombre') ? ' is-invalid' : '' }}"
                                        value="{{ old('nombre', $compania->nombre) }}"
                                        placeholder="Nombre de la compañía" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" id="telefono" name="telefono"
                                        class="form-control{{ $errors->has('telefono') ? ' is-invalid' : '' }}"
                                        value="{{ old('telefono', $compania->telefono) }}"
                                        placeholder="Teléfono" required>
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="correo" class="form-label">Correo Electrónico</label>
                                    <input type="email" id="correo" name="correo"
                                        class="form-control{{ $errors->has('correo') ? ' is-invalid' : '' }}"
                                        value="{{ old('correo', $compania->correo) }}"
                                        placeholder="correo@ejemplo.com" required>
                                    @error('correo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <input type="text" id="direccion" name="direccion"
                                        class="form-control{{ $errors->has('direccion') ? ' is-invalid' : '' }}"
                                        value="{{ old('direccion', $compania->direccion) }}"
                                        placeholder="Dirección completa" required>
                                    @error('direccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>{{ __('Guardar Cambios') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación de formulario
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Cerrar alertas automáticamente después de 5 segundos
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 5000);
    </script>
@stop
