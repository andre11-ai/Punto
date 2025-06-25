<?php

namespace App\Http\Controllers;

use App\Models\Proveedores;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Promocion;

class PromocionController extends Controller
{

        public function index()
    {
        $this->eliminarPromocionesExpiradas();

        $productosConPromocion = Producto::with(['promocion', 'categoria', 'proveedor'])
            ->whereHas('promocion', function($query) {
                $query->where('fecha_fin', '>=', now());
            })
            ->get()
            ->filter(function($producto) {
                return $producto->promocion && $producto->promocion->fecha_fin >= now();
            });

        return view('productos.index', compact('productosConPromocion'));
    }

    protected function eliminarPromocionesExpiradas()
    {
        $now = now();

        $expiradas = Promocion::where('fecha_fin', '<', $now)->get();

        foreach ($expiradas as $promocion) {
            Producto::where('promocion_id', $promocion->id)
                ->update(['promocion_id' => null]);

            $promocion->delete();
        }
    }

    public function guardar(Request $request)
    {
        $validated = $request->validate([
            'tipo_promocion' => 'required|string|not_in:ninguna',
            'fecha_fin' => 'required|date|after_or_equal:today',
            'productos' => 'required|array',
            'productos.*' => 'exists:productos,id',
            'id_proveedor' => 'nullable|exists:proveedores,id',
            'id_categoria' => 'nullable|exists:categorias,id'
        ]);

        $promocion = Promocion::create([
            'tipo' => $validated['tipo_promocion'],
            'fecha_inicio' => now(),
            'fecha_fin' => $validated['fecha_fin'],
            'id_proveedor' => $validated['id_proveedor'],
            'id_categoria' => $validated['id_categoria']
        ]);

        Producto::whereIn('id', $validated['productos'])
            ->update(['promocion_id' => $promocion->id]);

        return redirect()->route('productos.index')
            ->with('success', 'Promoción aplicada a los productos seleccionados');
    }


        public function update(Request $request, $id)
    {
        $promocion = Promocion::findOrFail($id);

        $request->validate([
            'tipo_promocion' => 'required',
            'fecha_fin' => 'required|date|after_or_equal:today',
            'productos_seleccionados' => 'required|array',
        ]);

        $productos = json_decode($request->productos_seleccionados, true);
        $productosSeleccionados = $request->input('productos', []);
        $tipoPromocion = $request->input('tipo_promocion');

        $promocion->tipo = $tipoPromocion;
        $promocion->save();

        $productosActuales = Producto::where('promocion_id', $promocion->id)->get();

        foreach ($productosActuales as $producto) {
            if (!in_array($producto->id, $productosSeleccionados)) {
                $producto->promocion_id = null;
                $producto->save();
            }
        }

        foreach ($productosSeleccionados as $productoId) {
            $producto = Producto::find($productoId);
            if ($producto) {
                $producto->promocion_id = $promocion->id;
                $producto->save();
            }
        }

        return redirect()->route('productos.index')->with('success', 'Promoción actualizada correctamente');
    }

    public function getProveedores()
    {
        $proveedores = Proveedores::all(['id', 'nombre']);
        return response()->json($proveedores);
    }

    public function getCategorias($proveedorId)
    {
        try {
            $proveedor = Proveedores::find($proveedorId);

            if (!$proveedor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proveedor no encontrado'
                ], 404);
            }

            $categorias = $proveedor->categorias()->select('id', 'nombre')->get();

            return response()->json([
                'success' => true,
                'data' => $categorias
            ]);

        } catch (\Exception $e) {
            \Log::error("Error en getCategorias: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar categorías',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function getProductos($categoriaId)
    {
        $productos = Producto::where('id_categoria', $categoriaId)->get(['id', 'producto']);
        return response()->json($productos);
    }

    public function verificarExpiradas()
    {
        $count = 0;
        $promocionesExpiradas = Promocion::where('fecha_fin', '<', now())->get();

        foreach ($promocionesExpiradas as $promocion) {
            Producto::where('promocion_id', $promocion->id)->update(['promocion_id' => null]);
            $promocion->delete();
            $count++;
        }

        return response()->json(['eliminadas' => $count]);
    }

}
