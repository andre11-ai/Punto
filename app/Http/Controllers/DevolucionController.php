<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\DevolucionDetalle;
use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
public function store(Request $request, Venta $venta)
{
    $request->validate([
        'motivo' => 'nullable|string|max:255',
        'productos' => 'required|array',
        'productos.*.producto_id' => 'required|exists:productos,id',
        'productos.*.cantidad' => 'required|integer|min:1',
        'productos.*.precio' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();
    try {
        $devolucion = Devolucion::create([
            'venta_id' => $venta->id,
            'user_id' => auth()->id(),
            'motivo' => $request->motivo,
        ]);

        foreach ($request->productos as $prod) {
            if ($prod['cantidad'] > 0) {
                DevolucionDetalle::create([
                    'devolucion_id' => $devolucion->id,
                    'producto_id' => $prod['producto_id'],
                    'cantidad' => $prod['cantidad'],
                    'precio' => $prod['precio'],
                ]);
                // Sumar al inventario
                $producto = Producto::find($prod['producto_id']);
                $producto->sku += $prod['cantidad'];
                $producto->save();
            }
        }

        DB::commit();
        return back()->with('success', 'Devolución registrada correctamente.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', 'Error al registrar devolución: ' . $e->getMessage());
    }
}

    public function historial(Venta $venta)
    {
        $devoluciones = Devolucion::where('venta_id', $venta->id)->with('detalles.producto', 'user')->get();
        return view('venta.devoluciones', compact('venta', 'devoluciones'));
    }
}
