@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1><i class="fas fa-users me-2"></i>Usuarios</h1>
@stop

@section('css')
    <link href="DataTables/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        #tblUsers {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        #tblUsers thead th {
            background-color: #3490dc;
            color: white;
            font-weight: 500;
        }

        #tblUsers tbody tr:hover {
            background-color: rgba(52, 144, 220, 0.1);
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 4px;
        }

        .card-header {
            background-color: #3490dc !important;
            color: white !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .alert-success {
            border-left: 4px solid #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }

        .alert-success strong {
            color: #28a745;
        }

        .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        .btn-close:hover {
            opacity: 1;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .badge-role {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        .badge-admin {
            background-color: #dc3545;
            color: white;
        }

        .badge-user {
            background-color: #28a745;
            color: white;
        }

        .badge-manager {
            background-color: #fd7e14;
            color: white;
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
            <i class="fas fa-user-friends me-2"></i>{{ __('Usuarios') }}
          </span>
          <div class="float-right">
            <a href="{{ route('usuarios.create') }}" class="btn btn-success btn-sm">
              <i class="fas fa-plus-circle me-1"></i>{{ __('Nuevo Usuario') }}
            </a>
          </div>
        </div>
      </div>

      <div class="card-body">
        @if ($message = Session::get('success'))
          <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <strong>{{ $message }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="table-responsive">
          <table id="tblUsers" class="table table-striped table-hover display responsive nowrap" width="100%">
            <thead class="thead">
              <tr>
                <th><i class="fas fa-hashtag me-1"></i>ID</th>
                <th><i class="fas fa-user me-1"></i>Nombre</th>
                <th><i class="fas fa-envelope me-1"></i>Correo</th>
                <th><i class="fas fa-clock me-1"></i>Turno</th>       {{-- <-- Nueva columna --}}
                <th><i class="fas fa-user-tag me-1"></i>Rol</th>
                <th><i class="fas fa-cogs me-1"></i>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($usuarios as $usuario)
                <tr>
                  <td>{{ $usuario->id }}</td>
                  <td>{{ $usuario->name }}</td>
                  <td>{{ $usuario->email }}</td>
                  <td>                                           {{-- ← Nueva celda Turno --}}
                    @if($usuario->turno === 'Matutino')
                      <span class="badge bg-info">Matutino</span>
                    @elseif($usuario->turno === 'Vespertino')
                      <span class="badge bg-warning">Vespertino</span>
                    @else
                      <span class="badge bg-secondary">Mixto</span>
                    @endif
                  </td>
                  <td>
                    @php
                      $badgeClass = '';
                      switch(strtolower($usuario->rol)) {
                        case 'admin': $badgeClass = 'badge-admin'; break;
                        case 'user': $badgeClass = 'badge-user'; break;
                        case 'manager': $badgeClass = 'badge-manager'; break;
                        default: $badgeClass = 'bg-secondary';
                      }
                    @endphp
                    <span class="badge {{ $badgeClass }} badge-role">
                      {{ $usuario->rol }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex gap-2">
                      <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-primary btn-action">
                        <i class="fas fa-edit me-1"></i>Editar
                      </a>
                      <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="form-eliminar">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger btn-action">
                          <i class="fas fa-trash-alt me-1"></i>Eliminar
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="empty-state">
                    <i class="fas fa-user-slash fa-2x"></i>
                    <h5 class="mt-3">No hay usuarios registrados</h5>
                    <p class="text-muted">Comienza agregando un nuevo usuario</p>
                    <a href="{{ route('usuarios.create') }}" class="btn btn-primary mt-2">
                      <i class="fas fa-plus me-1"></i>Agregar Usuario
                    </a>
                  </td>
                </tr>
                +                <tr>
                  <td colspan="6" class="empty-state"> {{-- Aumentamos a 6 --}}
                    <i class="fas fa-user-slash fa-2x"></i>
                    <h5 class="mt-3">No hay usuarios registrados</h5>
                    <p class="text-muted">Comienza agregando un nuevo usuario</p>
                    <a href="{{ route('usuarios.create') }}" class="btn btn-primary mt-2">
                      <i class="fas fa-plus me-1"></i>Agregar Usuario
                    </a>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@stop

@section('js')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="DataTables/datatables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {
      // Configuración de DataTables
      const table = new DataTable('#tblUsers', {
        responsive: true,
        fixedHeader: true,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        initComplete: function() {
          $('.dataTables_filter input').addClass('form-control form-control-sm');
        }
      });

      // Eliminar usuario con SweetAlert
      $(document).on('submit', '.form-eliminar', function(e) {
        e.preventDefault();
        const form = $(this);

        Swal.fire({
          title: '¿Eliminar usuario?',
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
              '¡Eliminado!',
              'El usuario ha sido eliminado.',
              'success'
            ).then(() => {
              table.row(form.parents('tr')).remove().draw();
            });
          }
        });
      });
    });
  </script>
@stop
