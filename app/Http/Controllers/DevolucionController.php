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
public function store(Request $request)
{
    // Filtrar productos para solo enviar los que realmente se devuelven (cantidad > 0)
    $productos = collect($request->input('productos', []))
        ->filter(function($p) {
            return isset($p['cantidad']) && intval($p['cantidad']) > 0;
        })->values()->all();

    $request->merge(['productos' => $productos]);

    $rules = [
        'productos' => 'required|array|min:1',
        'productos.*.producto_id' => ['required', 'exists:productos,id'],
        'productos.*.cantidad'    => ['required', 'integer', 'min:1'],
    ];

    $validated = $request->validate($rules);

    foreach ($validated['productos'] as $p) {
        $producto = \App\Models\Producto::find($p['producto_id']);
        if (!$producto->devolucion) {
            return back()->withErrors(['productos' => "El producto {$producto->nombre} no se puede devolver."]);
        }
    }

    DB::beginTransaction();
    try {
        // Guardar la devolución PRIMERO para tener el ID
        $devolucion = new \App\Models\Devolucion();
        $devolucion->venta_id = $request->venta_id;
        $devolucion->user_id = auth()->id();
        $devolucion->motivo = $request->motivo;
        $devolucion->save();

        $totalDevolucion = 0;

        // Guardar los detalles de la devolución y actualizar stock
        foreach ($validated['productos'] as $p) {
            // SIEMPRE buscar el precio en la venta original
            $detalleVenta = \App\Models\Detalleventa::where('id_venta', $request->venta_id)
                ->where('id_producto', $p['producto_id'])
                ->first();
            $precio = $detalleVenta ? $detalleVenta->precio : 0;

            $detalle = new \App\Models\DevolucionDetalle();
            $detalle->devolucion_id = $devolucion->id;
            $detalle->producto_id = $p['producto_id'];
            $detalle->cantidad = $p['cantidad'];
            $detalle->precio = $precio;
            $detalle->save();

            // Actualizar inventario (sumar la cantidad devuelta)
            $producto = \App\Models\Producto::find($p['producto_id']);
            if ($producto) {
                $producto->sku += $p['cantidad'];
                $producto->save();
            }

            $totalDevolucion += $precio * $p['cantidad'];
        }

        // Registrar la "venta" de tipo devolucion (con monto negativo)
        $ventaDevolucion = \App\Models\Venta::create([
            'total' => -abs($totalDevolucion), // monto negativo
            'pago_recibido' => 0,
            'id_usuario' => auth()->id(),
            'metodo_pago' => 'efectivo',
            'tipo' => 'devolucion',
            'cliente_id' => null
        ]);

        // Registrar detalles de la venta devolucion
        foreach ($validated['productos'] as $p) {
            // SIEMPRE buscar el precio en la venta original
            $detalleVenta = \App\Models\Detalleventa::where('id_venta', $request->venta_id)
                ->where('id_producto', $p['producto_id'])
                ->first();
            $precio = $detalleVenta ? $detalleVenta->precio : 0;

            \App\Models\Detalleventa::create([
                'precio' => $precio,
                'cantidad' => $p['cantidad'],
                'id_producto' => $p['producto_id'],
                'id_venta' => $ventaDevolucion->id
            ]);
        }

        DB::commit();
        return response()->json(['message' => 'Devolución procesada correctamente.']);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error al procesar la devolución: '.$e->getMessage()], 500);
    }
}

public function historial(Venta $venta)
{
    $devoluciones = \App\Models\Devolucion::where('venta_id', $venta->id)
        ->with('detalles.producto', 'user')
        ->orderByDesc('created_at')
        ->get();

    // Si es petición AJAX (JS), devuelve JSON:
    if (request()->ajax()) {
        return response()->json($devoluciones);
    }
    // Si es vista, renderiza el blade
    return view('venta.devoluciones', compact('venta', 'devoluciones'));
}
    public function form(Venta $venta)
{
    // Carga los productos de la venta y pásalos a la vista
    $venta->load('detalles.producto');
    return view('venta.form_devolucion', compact('venta'));
}

// ticket para devolucion (PDF)
public function ticket($id)
{
    $devolucion = \App\Models\Devolucion::with(['user', 'detalles.producto', 'venta'])->findOrFail($id);

    // En tu método ticket del controlador DevolucionController:
$productos = $devolucion->detalles->map(function ($detalle) {
    return (object)[
        'cantidad' => $detalle->cantidad,
        'producto' => $detalle->producto->producto ?? 'Producto eliminado',
        'precio'   => number_format($detalle->precio, 2, '.', ''),
        'total'    => number_format($detalle->precio * $detalle->cantidad, 2, '.', ''),
    ];
});

$totalDevolucion = $devolucion->detalles->sum(function ($detalle) {
    return $detalle->precio * $detalle->cantidad;
});
$totalProductos = $devolucion->detalles->sum('cantidad');

$formatter = new \Luecano\NumeroALetras\NumeroALetras();
$totalLetras = strtoupper($formatter->toMoney($totalDevolucion, 2, 'PESOS', 'CENTAVOS'));

$data = [
    'devolucion' => $devolucion,
    'venta' => $devolucion->venta,
    'productos' => $productos,
    'total' => number_format($totalDevolucion, 2, '.', ''),
    'total_negativo' => number_format($totalDevolucion * -1, 2, '.', ''),
    'total_letras' => $totalLetras,
    'total_productos' => $totalProductos,
    'fecha' => $devolucion->created_at,
    'usuario' => $devolucion->user->name ?? 'Desconocido',
];
    return \Barryvdh\DomPDF\Facade\Pdf::loadView('venta.ticket-devolucion', $data)
        ->setPaper([0, 0, 250, 700], 'portrait')
        ->setWarnings(false)
        ->stream("ticket_devolucion_{$devolucion->id}.pdf");
}


}
