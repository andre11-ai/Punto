<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // Roles permitidos
    protected $roles = [
        'Admin'       => 'Admin',
        'User'        => 'User',
    ];

    /**
     * Devuelve JSON para DataTables.
     */
    public function list()
    {
        $usuarios = User::select(['id', 'name', 'email', 'rol'])
                        ->orderBy('id', 'desc')
                        ->get();

        return response()->json(['data' => $usuarios]);
    }

    /**
     * Muestra la vista de listado (la tabla la llena JS).
     */
    public function index()
    {

    $usuarios = User::all(); // obtener todos los usuarios
    return view('usuario.index', compact('usuarios'));
}

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $usuario = new User();
        $roles   = $this->roles;
        return view('usuario.create', compact('usuario', 'roles'));
    }

    /**
     * Almacena un nuevo usuario.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'regex:/^[\pL\s]+$/u', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'rol'      => ['required', 'in:,Admin,User'],
            'turno'    => ['required', 'in:Matutino,Vespertino,Mixto'], // nuevo campo turno
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario creado correctamente');
    }

    /**
     * Muestra un solo usuario (opcional).
     */
    public function show(User $usuario)
    {
        return view('usuario.show', compact('usuario'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(User $usuario)
    {
        $roles = $this->roles;
        return view('usuario.edit', compact('usuario', 'roles'));
    }

    /**
     * Actualiza un usuario existente.
     */
    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name'     => ['required', 'regex:/^[\pL\s]+$/u', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email,' . $usuario->id],
            'password' => ['nullable', 'string', 'min:6'],
            'rol'      => ['required', 'in:Super-Admin,Admin,User'],
            'turno'    => ['required', 'in:Matutino,Vespertino,Mixto'], // nuevo campo turno
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Elimina un usuario.
     */
    public function destroy(User $usuario)
    {
        $usuario->delete();
        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario eliminado correctamente');
    }
}
