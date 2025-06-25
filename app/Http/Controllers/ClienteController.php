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
    $request->validate([
        'metodo' => 'required|in:efectivo,tarjeta',
        'monto' => 'required|numeric|min:1'
    ]);

    $cliente = Cliente::findOrFail($id);
    $original = $cliente->total_compra;

    if ($cliente->total_compra <= 0) {
        return response()->json(['success' => false, 'message' => 'Este cliente no tiene deuda.']);
    }

    $montoAbonado = $request->monto;
    $cambio = 0;

    // Si paga de más y es efectivo, calcular cambio
    if ($request->metodo === 'efectivo' && $montoAbonado > $cliente->total_compra) {
        $cambio = $montoAbonado - $cliente->total_compra;
    }

    // Descontar abono y ajustar deuda
    $cliente->total_compra -= $montoAbonado;
    if ($cliente->total_compra < 0) {
        $cliente->total_compra = 0;
    }
    $cliente->save();

    // Simulación de ticket
    $ticketUrl = route('ventas.ticket.abono', [
        'id' => $cliente->id,
        'monto' => $original,
        'abono' => $request->monto,
        'cambio' => $cambio
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Abono registrado correctamente',
        'ticket_url' => $ticketUrl
    ]);
}

public function ticketAbono($id, Request $request)
{
    $venta = Venta::with('user')->findOrFail($idVenta);
    $cliente = Cliente::findOrFail($venta->cliente_id);
    $company = Compania::first();

    $montoInicial = $venta->total + ($venta->descuento ?? 0);
    $deuda_original = $venta->deuda_original ?? $montoInicial;
    $total_letras = NumeroALetras::convertir($venta->total, 'PESOS', 'CENTAVOS');

    return view('ticket-abono', [
        'venta' => $venta,
        'cliente' => $cliente,
        'company' => $company,
        'montoInicial' => $montoInicial,
        'deuda_original' => $deuda_original,
        'total_letras' => $total_letras
    ]);


}






    public function abonar(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        $monto = floatval($request->input('monto'));
        $metodo = $request->input('metodo');
        $user = Auth::user();

        // Actualizar deuda actual
        $cliente->total_compra -= $monto;

        // Si es la primera vez que abona, guardar la deuda inicial
        if (!$cliente->deuda_inicial) {
            $cliente->deuda_inicial = $cliente->total_compra + $monto;
        }

        // Si ya se liquidó
        if ($cliente->total_compra <= 0) {
            $cliente->total_compra = 0;

            // Registrar como venta oficial
            $venta = Venta::create([
                'total' => $cliente->deuda_inicial,
                'pago_recibido' => $cliente->deuda_inicial,
                'id_usuario' => $user->id,
                'metodo_pago' => $metodo,
            ]);

            // Aquí puedes personalizar qué productos deseas asignar
            // Por ejemplo, si quieres replicar lo que compró en su momento:
            $productos = session('productos_cliente_' . $cliente->id, []); // Asegúrate de guardar esto al crear la deuda

            foreach ($productos as $producto) {
                Detalleventa::create([
                    'precio' => $producto['precio'],
                    'cantidad' => $producto['cantidad'],
                    'id_producto' => $producto['id_producto'],
                    'id_venta' => $venta->id,
                ]);
            }

            $cliente->delete(); // Eliminar al cliente de deudores

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

    // ⚠️ Define estas 3 variables:
    $montoInicial = $venta->monto_inicial ?? 0;
    $deuda_original = $venta->deuda_original ?? $venta->total_compra + $montoInicial;
    $totalEnLetras = (new NumeroALetras)->toWords($venta->total);

    return view('venta.ticket-abono', compact('venta', 'productos', 'montoInicial', 'deuda_original', 'totalEnLetras'));
}
}
