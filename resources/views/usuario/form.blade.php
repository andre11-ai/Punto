<div class="card border-primary">
  <div class="card-header bg-primary text-white">
    <h4 class="mb-0">
      <i class="fas fa-user-edit me-2"></i>
      {{ isset($usuario->id) ? 'Editar Usuario' : 'Nuevo Usuario' }}
    </h4>
  </div>

  <div class="card-body">
    <div class="row">
      {{-- Nombre --}}
      <div class="form-group col-md-6 mb-4">
        <label for="name" class="form-label fw-bold text-primary">
          <i class="fas fa-user me-1"></i> Nombre
        </label>
        <div class="input-group">
          <span class="input-group-text bg-light">
            <i class="fas fa-user text-primary"></i>
          </span>
          <input
            type="text"
            name="name"
            id="name"
            class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
            value="{{ old('name', $usuario->name) }}"
            placeholder="Nombre completo"
            autofocus
          />
        </div>
        @error('name')
          <div class="invalid-feedback d-block">
            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
          </div>
        @enderror
      </div>

      {{-- Correo --}}
      <div class="form-group col-md-6 mb-4">
        <label for="email" class="form-label fw-bold text-primary">
          <i class="fas fa-envelope me-1"></i> Correo
        </label>
        <div class="input-group">
          <span class="input-group-text bg-light">
            <i class="fas fa-at text-primary"></i>
          </span>
          <input
            type="email"
            name="email"
            id="email"
            class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
            value="{{ old('email', $usuario->email) }}"
            placeholder="email@dominio.com"
          />
        </div>
        @error('email')
          <div class="invalid-feedback d-block">
            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
          </div>
        @enderror
      </div>

      {{-- Rol --}}
      <div class="form-group col-md-6 mb-4">
        <label for="rol" class="form-label fw-bold text-primary">
          <i class="fas fa-user-tag me-1"></i> Rol de usuario
        </label>
        <div class="input-group">
          <span class="input-group-text bg-light">
            <i class="fas fa-shield-alt text-primary"></i>
          </span>
          <select
            name="rol"
            id="rol"
            class="form-control{{ $errors->has('rol') ? ' is-invalid' : '' }}"
          >
            <option value="">Selecciona un rol</option>
            @foreach($roles as $value => $label)
              <option
                value="{{ $value }}"
                {{ old('rol', $usuario->rol) === $value ? 'selected' : '' }}
              >{{ $label }}</option>
            @endforeach
          </select>
        </div>
        @error('rol')
          <div class="invalid-feedback d-block">
            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
          </div>
        @enderror
      </div>

    {{-- Turno --}}
    <div class="form-group col-md-6 mb-4">
      <label for="turno" class="form-label fw-bold text-primary">
        <i class="fas fa-clock me-1"></i> Turno
      </label>
      <div class="input-group">
        <span class="input-group-text bg-light">
          <i class="fas fa-clock text-primary"></i>
        </span>
        <select
          name="turno"
          id="turno"
          class="form-control{{ $errors->has('turno') ? ' is-invalid' : '' }}"
        >
          <option value="">Selecciona un turno</option>
          <option value="Matutino"  {{ old('turno', $usuario->turno) === 'Matutino'  ? 'selected' : '' }}>Matutino</option>
          <option value="Vespertino" {{ old('turno', $usuario->turno) === 'Vespertino' ? 'selected' : '' }}>Vespertino</option>
          <option value="Mixto"      {{ old('turno', $usuario->turno) === 'Mixto'      ? 'selected' : '' }}>Mixto</option>
        </select>
      </div>
      @error('turno')
        <div class="invalid-feedback d-block">
          <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
        </div>
      @enderror
    </div>

      {{-- Contraseña --}}
      <div class="form-group col-md-6 mb-4">
        <label for="password" class="form-label fw-bold text-primary">
          <i class="fas fa-key me-1"></i> Contraseña
        </label>
        <div class="input-group">
          <span class="input-group-text bg-light">
            <i class="fas fa-lock text-primary"></i>
          </span>
          <input
            type="password"
            name="password"
            id="password"
            class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
            placeholder="{{ isset($usuario->id) ? 'Dejar vacío para no cambiar' : 'Contraseña' }}"
          />
          <button class="btn btn-outline-secondary" type="button" id="togglePassword">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        @error('password')
          <div class="invalid-feedback d-block">
            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
          </div>
        @enderror
      </div>
    </div>
  </div>

  <div class="card-footer bg-light">
    <div class="d-flex justify-content-between">
      <a href="{{ route('usuarios.index') }}" class="btn btn-danger">
        <i class="fas fa-times-circle me-2"></i> Cancelar
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-2"></i>
        {{ isset($usuario->id) ? 'Actualizar Usuario' : 'Guardar Usuario' }}
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

  #togglePassword {
    border-left: 0;
  }
</style>
@endpush

@push('js')
<script>
  $(document).ready(function() {
    // Mostrar/ocultar contraseña
    $('#togglePassword').click(function() {
      const passwordInput = $('#password');
      const icon = $(this).find('i');

      if (passwordInput.attr('type') === 'password') {
        passwordInput.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
      } else {
        passwordInput.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
      }
    });

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
  });
</script>
@endpush
