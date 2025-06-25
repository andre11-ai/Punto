<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedores;
use Illuminate\Http\Request;

class ProveedoresController extends Controller
{
    public function index()
    {
        $proveedores = Proveedores::all();
        return view('proveedor.index', compact('proveedores'));
    }

    public function list()
    {
        $proveedores = Proveedores::all();

        return response()->json([
            'data' => $proveedores->map(function ($prov) {
                return [
                    'id' => $prov->id,
                    'nombre' => $prov->nombre,
                    'upc' => $prov->upc,
                    'acciones' => view('proveedor.partials.acciones', compact('prov'))->render()
                ];
            })
        ]);
    }


    public function create()
    {
        $proveedor = new Proveedores();
        return view('proveedor.create', compact('proveedor'));
    }

    public function store(Request $request)
    {
        request()->validate(Proveedores::$rules);

        Proveedores::create([
            'nombre' => $request->nombre,
            'upc' => $request->upc
        ]);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado');
    }

    public function show($id)
    {
        $proveedor = Proveedores::find($id);
        return view('proveedor.show', compact('proveedor'));
    }

    public function edit($id)
    {
        $proveedor = Proveedores::find($id);
        return view('proveedor.edit', compact('proveedor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'upc' => 'required'
        ]);

        $proveedor = Proveedores::findOrFail($id);
        $proveedor->update([
            'nombre' => $request->nombre,
            'upc' => $request->upc
        ]);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }
    public function destroy($id)
    {
        try {
            $proveedor = Proveedores::findOrFail($id);
            $proveedor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el proveedor: ' . $e->getMessage()
            ], 500);
        }
    }
}
