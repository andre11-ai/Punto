<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Compania;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Luecano\NumeroALetras\NumeroALetras;
use Illuminate\Support\Facades\Auth;




class ClienteController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clientes = Cliente::where('total_compra', '>', 0)
                            ->select(['id', 'nombre', 'telefono', 'created_at', 'total_compra']);

            $clientes = Cliente::where('total_compra', '>', 0)->select(['id', 'nombre', 'telefono', 'created_at', 'total_compra']);


            return DataTables::of($clientes)
                ->addColumn('total_compra', function ($cliente) {
                    return '$' . number_format($cliente->total_compra ?? 0, 2);
                })
                ->addColumn('dias_sin_pagar', function ($cliente) {
                    $fecha = $cliente->created_at ?? now();
                    $ahora = Carbon::now();

                    return $fecha->greaterThan($ahora)
                        ? 'Fecha futura'
                        : $fecha->diffForHumans($ahora, [
                            'parts' => 2,
                            'join' => true,
                            'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
                            'options' => Carbon::JUST_NOW | Carbon::ONE_DAY_WORDS
                        ]);
                })
                ->addColumn('acciones', function ($cliente) {
                    return '<button class="btn btn-sm btn-success btn-abonar"
                                data-id="' . $cliente->id . '"
                                data-nombre="' . e($cliente->nombre) . '"
                                data-total="' . number_format($cliente->total_compra, 2, '.', '') . '">
                                Abonar</button>';
                })
                ->rawColumns(['dias_sin_pagar', 'acciones'])
                ->make(true);
        }

        return view('cliente.index');
    }


    public function show($id)
    {
        $cliente = Cliente::find($id);

        return view('cliente.show', compact('cliente'));
    }


    public function guardarDeudor(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:191',
            'telefono' => 'required|string|max:30',
            'total_compra' => 'required|numeric|min:0'
        ]);

        $cliente = Cliente::where('nombre', $request->nombre)
                        ->where('telefono', $request->telefono)
                        ->first();

        if (!$cliente) {
            $cliente = new Cliente();
            $cliente->nombre = $request->nombre;
            $cliente->telefono = $request->telefono;
        }

        if (!$cliente->fecha_deuda) {
            $cliente->fecha_deuda = now();
        }

        $cliente->total_compra = $request->total_compra;
        $cliente->deuda_inicial = $request->total_compra;
        $cliente->save();

        return response()->json([
            'success' => true,
            'message' => 'Deudor registrado correctamente',
            'cliente' => $cliente
        ]);
    }


    public function listarClientes()
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        try {
            $clientes = Cliente::whereNotNull('fecha_deuda')->get()->map(function ($cliente) {
                return [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'telefono' => $cliente->telefono,
                    'dias_sin_pagar' => $cliente->fecha_deuda ? \Carbon\Carbon::parse($cliente->fecha_deuda)->diffInDays(now()) : null,
                ];
            });

            return response()->json(['data' => $clientes]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function registrarAbono(Request $request, $id)
    {
        try {
            $request->validate([
                'metodo' => 'required|in:efectivo,tarjeta',
                'monto' => 'required|numeric|min:1'
            ]);

            $cliente = \App\Models\Cliente::findOrFail($id);
            $montoAbonado = floatval($request->input('monto'));
            $metodo = $request->input('metodo') ?? 'efectivo';
            $original = $cliente->total_compra;
            $cambio = 0;

            if ($cliente->total_compra <= 0) {
                return response()->json(['success' => false, 'message' => 'Este cliente no tiene deuda.']);
            }

            if ($metodo === 'efectivo' && $montoAbonado > $cliente->total_compra) {
                $cambio = $montoAbonado - $cliente->total_compra;
            }

            $cliente->total_compra -= $montoAbonado;
            if ($cliente->total_compra < 0) $cliente->total_compra = 0;
            $cliente->save();

            $venta = \App\Models\Venta::create([
                'total' => $montoAbonado,
                'pago_recibido' => $montoAbonado,
                'id_usuario' => auth()->id(),
                'metodo_pago' => $metodo,
                'tipo' => 'abono',
                'cliente_id' => $cliente->id
            ]);

            $productoAbono = \App\Models\Producto::firstOrCreate(
                ['producto' => 'Abono a deuda'],
                [
                    'precio' => 0,
                    'codigo' => 'ABONO',
                    'precio_compra' => 0,
                    'precio_venta' => 0,
                    'id_categoria' => 1,
            'codigo_barras' => 'ABONO'
            ]
            );

            \App\Models\Detalleventa::create([
                'precio' => $montoAbonado,
                'cantidad' => 1,
                'id_producto' => $productoAbono->id,
                'id_venta' => $venta->id,
                'descripcion' => 'Abono a deuda'
            ]);

            if ($cliente->total_compra <= 0) {
                $cliente->delete();
            }

            $ticketUrl = route('ventas.ticket.abono', [
                'id' => $venta->id,
                'monto' => $montoAbonado,
                'abono' => $montoAbonado,
                'cambio' => $cambio
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Abono registrado correctamente',
                'ticket_url' => $ticketUrl,
                'venta_id' => $venta->id,
                'cliente_id' => $cliente->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function abonar(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        $monto = floatval($request->input('monto'));
        $metodo = $request->input('metodo');
        $user = Auth::user();

        $cliente->total_compra -= $monto;

        if (!$cliente->deuda_inicial) {
            $cliente->deuda_inicial = $cliente->total_compra + $monto;
        }

        if ($cliente->total_compra <= 0) {
            $cliente->total_compra = 0;

            $venta = Venta::create([
                'total' => $cliente->deuda_inicial,
                'pago_recibido' => $cliente->deuda_inicial,
                'id_usuario' => $user->id,
                'metodo_pago' => $metodo,
            ]);


            $productos = session('productos_cliente_' . $cliente->id, []);

            foreach ($productos as $producto) {
                Detalleventa::create([
                    'precio' => $producto['precio'],
                    'cantidad' => $producto['cantidad'],
                    'id_producto' => $producto['id_producto'],
                    'id_venta' => $venta->id,
                ]);
            }

            $cliente->delete();

            return response()->json([
                'success' => true,
                'finalizado' => true,
                'ticket_url' => route('venta.ticket', $venta->id),
            ]);
        } else {
            $cliente->save();

            return response()->json([
                'success' => true,
                'finalizado' => false,
            ]);
        }
    }



    public function realizarAbono(Request $request)
    {
        $cliente = Cliente::findOrFail($request->cliente_id);
        $monto = floatval($request->monto);

        $cliente->total_compra -= $monto;
        $cliente->save();

        if ($cliente->total_compra <= 0) {
            $cliente->delete();
        }

        return response()->json(['success' => true, 'message' => 'Abono procesado correctamente.']);
    }

    public function mostrarTicketAbono($id)
    {
    $venta = Venta::with('user')->findOrFail($id);
    $productos = DetalleDeuda::where('venta_id', $venta->id)->get();

    $montoInicial = $venta->monto_inicial ?? 0;
    $deuda_original = $venta->deuda_original ?? $venta->total_compra + $montoInicial;
    $totalEnLetras = (new NumeroALetras)->toWords($venta->total);

    return view('venta.ticket-abono', compact('venta', 'productos', 'montoInicial', 'deuda_original', 'totalEnLetras'));
    }


}
