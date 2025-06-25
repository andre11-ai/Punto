<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Compania;
use App\Models\Detalleventa;
use App\Models\DetalleDeuda;

use App\Models\Venta;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

use Luecano\NumeroALetras\NumeroALetras;


class VentaController extends Controller
{
    public function index()
    {
        return view('venta.index');
    }

public function procesarVenta($metodo = null, $recibido = null)
{
    if ($metodo !== null) {
        $this->metodoPago = is_array($metodo) ? $metodo['metodo'] : $metodo;
    }

    if ($recibido !== null && !is_array($recibido)) {
        $this->montoRecibido = floatval($recibido);
    }

    try {
        $cart = Cart::instance('shopping');

        if ($cart->count() === 0) {
            return ['success' => false, 'message' => 'El carrito está vacío'];
        }

        $total = $this->calcularTotal();

        // ⛔ Si es adeudo, no se crea venta directa
        if ($this->metodoPago === 'adeudo' && is_array($recibido)) {
            $cliente = Cliente::create([
                'nombre' => $recibido['nombre'],
                'telefono' => $recibido['telefono'],
                'direccion' => '',
                'fecha_deuda' => now(),
                'deuda_inicial' => $total,
                'total_compra' => $total,
            ]);

            foreach ($cart->content() as $item) {
                DetalleDeuda::create([
                    'cliente_id' => $cliente->id,
                    'producto_id' => $item->id,
                    'precio' => $item->price,
                    'cantidad' => $item->qty,
                    'promocion_aplicada' => $item->options->promocion ?? null,
                ]);
            }

            $cart->destroy();
            $this->refreshCart();

            return [
                'success' => true,
                'cliente_id' => $cliente->id
            ];
        }

        // ✔️ Venta directa (efectivo, tarjeta)
        $pagoRecibido = $this->metodoPago === 'efectivo'
            ? ($this->montoRecibido > 0 ? $this->montoRecibido : $total)
            : $total;

        $venta = Venta::create([
            'total' => $total,
            'pago_recibido' => $pagoRecibido,
            'id_usuario' => auth()->id(),
            'metodo_pago' => $this->metodoPago,
                'tipo' => 'venta'
        ]);

        foreach ($cart->content() as $item) {
            Detalleventa::create([
                'precio' => $item->price,
                'cantidad' => $item->qty,
                'id_producto' => $item->id,
                'id_venta' => $venta->id,
                'promocion_aplicada' => $item->options->promocion ?? null
            ]);
        }

        $cart->destroy();
        $this->refreshCart();

        return [
            'success' => true,
            'ticket' => $venta->id,
            'total' => $total
        ];

    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}


public function ticket($id)
{
    $data['company'] = Compania::first();
    $venta = Venta::with('user', 'detalles.producto')->findOrFail($id);
    $data['venta'] = $venta;

    // Fecha y hora formateada
    $fecha_venta = $venta->created_at;
    $data['fecha'] = date('d/m/Y', strtotime($fecha_venta));
    $data['hora']  = date('h:i A',    strtotime($fecha_venta));

    // 1) Lista de productos con precio final (con promos aplicadas)
    $data['productos'] = $venta->detalles->map(function ($detalle) {
        return (object)[
            'cantidad' => $detalle->cantidad,
            'producto' => $detalle->producto->producto ?? 'Producto eliminado',
            // precio ya con promo según tu lógica
            'precio'   => number_format($this->calcularPrecioFinal($detalle), 2, '.', ''),
        ];
    });

    // 2) Total de productos vendidos
    $data['total_productos'] = $venta->detalles->sum('cantidad');

    // 3) Subtotal original (sin promociones)
    $subtotalOriginal = $venta->detalles->sum(function ($detalle) {
        return $detalle->precio * $detalle->cantidad;
    });

    // 4) Total ya pagado (con promos)
    $totalConPromos = floatval($venta->total);

    // 5) Ahorro = diferencia
    $ahorro = $subtotalOriginal - $totalConPromos;
    $data['ahorro'] = number_format($ahorro, 2, '.', '');

    // 6) Cálculo del cambio (si aplica)
    $pagoRecibido = floatval($venta->pago_recibido ?? 0);
    $data['cambio'] = 0;
    if ($venta->metodo_pago === 'efectivo' && $pagoRecibido > $totalConPromos) {
        $data['cambio'] = round($pagoRecibido - $totalConPromos, 2);
    }

    $formatter = new NumeroALetras();
$data['total_letras'] = strtoupper(
    $formatter->toMoney(
        floatval($venta->total), // monto
        2,                       // decimales
        'PESOS',                 // nombre de la moneda
        'CENTAVOS'               // nombre de los centavos
    )
);

    // Finalmente, generar PDF
    $pdf = Pdf::loadView('venta.ticket', $data)
        ->setPaper([0, 0, 250, 700], 'portrait')
        ->setWarnings(false);

    return $pdf->stream("ticket_{$id}.pdf");
}

    private function calcularPrecioFinal($detalle)
    {
        $promo = strtolower($detalle->promocion_aplicada ?? '');
        $original = $detalle->precio;
        $qty = $detalle->cantidad;
        $subtotal = $original * $qty;

        return match ($promo) {
            '2x1' => ($qty > 1) ? ceil($qty / 2) * $original : $subtotal,
            '3x2' => (floor($qty / 3) * 2 + ($qty % 3)) * $original,
            '50%', '50% de descuento' => $subtotal * 0.5,
            'precio especial' => $subtotal * 0.85,
            'segunda unidad al 30%' => (floor($qty / 2) * ($original + $original * 0.3)) + (($qty % 2) * $original),
            default => $subtotal
        };
    }

    public function show()
    {
        // Ejemplo: traer todas las ventas, incluyendo las de tipo 'abono'
$ventas = \App\Models\Venta::whereIn('tipo', ['venta', 'abono'])->orderBy('created_at', 'desc')->get();
        return view('venta.show');

    }

    public function detalles()
    {
        return $this->hasMany(Detalleventa::class, 'id_venta');
    }

public function ticketAbono($id, Request $request)
{
    $cliente = \App\Models\Cliente::with(['detallesDeuda.producto'])->findOrFail($id);
    $company = \App\Models\Compania::first();
    $formatter = new \Luecano\NumeroALetras\NumeroALetras();



    $montoAbonado = floatval($request->input('abono') ?? $request->input('monto'));
    $metodoPago = $request->input('metodo') ?? 'efectivo';

    // Si no tienes deuda_inicial en la tabla, usa la suma de lo abonado más la deuda actual
    $deudaOriginal = $cliente->deuda_inicial ?? ($cliente->total_compra + $montoAbonado);
    $deudaAnterior = $cliente->total_compra + $montoAbonado;
    $deudaRestante = $cliente->total_compra;
    $productosVendidos = $cliente->detallesDeuda->sum('cantidad');
    $totalEnLetras = ucfirst($formatter->toMoney($montoAbonado, 2, 'PESOS', 'CENTAVOS'));
      $logoPath = public_path('storage/img/Logo-Negro.jpeg');

    return view('venta.ticket-abono', [
        'cliente' => $cliente,
        'company' => $company,
        'deuda_original' => $deudaOriginal,
        'montoInicial' => $deudaAnterior,
        'montoAbonado' => $montoAbonado,
        'deudaRestante' => $deudaRestante,
        'totalEnLetras' => $totalEnLetras,
        'productosVendidos' => $productosVendidos,
        'metodoPago' => $metodoPago,
        'fecha' => now(),
                'logoPath'

    ]);
}



public function listVentas()
{
    $ventas = Venta::select('id', 'total', 'created_at', 'tipo')
        ->orderByDesc('id')
        ->get()
        ->map(function ($venta) {
            return [
                'id' => $venta->id,
                'total' => '$' . number_format($venta->total, 2),
                'created_at' => $venta->created_at->format('Y-m-d H:i:s'),
                'tipo' => $venta->tipo === 'abono'
                    ? '<span class="badge bg-warning">Abono</span>'
                    : '<span class="badge bg-success">Venta</span>',
            ];
        });

    return response()->json(['data' => $ventas]);
}







public function registrarAbonoFinal($clienteId, $monto)
{
    $cliente = Cliente::with('detallesDeuda')->findOrFail($clienteId);

    // Crear una nueva venta
    $venta = Venta::create([
        'total' => $monto,
        'pago_recibido' => $monto,
        'id_usuario' => auth()->id(),
        'metodo_pago' => 'efectivo'
    ]);

    // Registrar los productos de detalle_deudas como detalleventa
    foreach ($cliente->detallesDeuda as $detalle) {
        Detalleventa::create([
            'precio' => $detalle->precio,
            'cantidad' => $detalle->cantidad,
            'id_producto' => $detalle->producto_id,
            'id_venta' => $venta->id,
            'promocion_aplicada' => $detalle->promocion_aplicada
        ]);
    }

    // Actualizar la deuda restante
    $cliente->total_compra -= $monto;
    $cliente->save();

    // Si ya no debe nada, eliminarlo
    if ($cliente->total_compra <= 0) {
        $cliente->delete();
    }

    return response()->json([
        'success' => true,
        'venta_id' => $venta->id
    ]);
}


public function ticketAbonoDesdeVenta($id)
{
    $venta = Venta::with('user', 'detalles.producto')->findOrFail($id);
    $company = Compania::first();
    $formatter = new NumeroALetras();
    $company = Compania::first(); // o la lógica correspondiente


    $fecha_venta = $venta->created_at;

    $productos = $venta->detalles->map(function ($detalle) {
        return (object)[
            'cantidad' => $detalle->cantidad,
            'producto' => $detalle->producto->producto ?? 'Producto eliminado',
            'precio'   => number_format($this->calcularPrecioFinal($detalle), 2, '.', ''),
        ];
    });

    $subtotalOriginal = $venta->detalles->sum(fn($d) => $d->precio * $d->cantidad);
    $totalConPromos = floatval($venta->total);
    $ahorro = $subtotalOriginal - $totalConPromos;

    $pagoRecibido = floatval($venta->pago_recibido ?? 0);
    $cambio = ($venta->metodo_pago === 'efectivo' && $pagoRecibido > $totalConPromos)
        ? round($pagoRecibido - $totalConPromos, 2)
        : 0;

    $data = [
        'venta' => $venta,
        'fecha' => date('d/m/Y', strtotime($fecha_venta)),
        'hora' => date('h:i A', strtotime($fecha_venta)),
        'productos' => $productos,
        'ahorro' => number_format($ahorro, 2, '.', ''),
        'total_productos' => $venta->detalles->sum('cantidad'),
        'cambio' => $cambio,
        'total_letras' => strtoupper($formatter->toMoney($venta->total, 2, 'PESOS', 'CENTAVOS')),
        'company' => $company,
        'desdeAbono' => true
    ];

    return Pdf::loadView('venta.ticket', $data)
        ->setPaper([0, 0, 250, 700], 'portrait')
        ->setWarnings(false)
        ->stream("ticket_abono_{$venta->id}.pdf");
}


public function mostrarTicket($id)
{
    $venta = Venta::with('user', 'detalles.producto')->findOrFail($id);
    $productos = $venta->detalles->map(function ($detalle) {
        return (object)[
            'cantidad' => $detalle->cantidad,
            'producto' => $detalle->producto->producto ?? 'Producto eliminado',
            'precio'   => number_format($detalle->precio, 2, '.', ''),
        ];
    });

    $company = \App\Models\Compania::first();
    $total_productos = $venta->detalles->sum('cantidad');
    $formatter = new \Luecano\NumeroALetras\NumeroALetras();
    $total_letras = strtoupper($formatter->toMoney(floatval($venta->total), 2, 'PESOS', 'CENTAVOS'));
    $pagoRecibido = $venta->pago_recibido ?? 0;
    $cambio = ($venta->metodo_pago === 'efectivo' && $pagoRecibido > $venta->total)
        ? round($pagoRecibido - $venta->total, 2)
        : 0;
    $subtotalOriginal = $venta->detalles->sum(function ($detalle) {
        return $detalle->precio * $detalle->cantidad;
    });
    $ahorro = $subtotalOriginal - floatval($venta->total);

    // Ruta absoluta para DomPDF (¡esto es lo importante!)
    $logoPath = public_path('storage/img/Logo-Colo.png');

    $data = compact(
        'venta',
        'productos',
        'company',
        'total_productos',
        'total_letras',
        'cambio',
        'ahorro',
        'pagoRecibido',
        'logoPath'
    );

    return \Pdf::loadView('venta.ticket', $data)
        ->setPaper([0, 0, 250, 700], 'portrait')
        ->setWarnings(false)
        ->stream("ticket_abono_{$venta->id}.pdf");
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
    if ($request->metodo === 'efectivo' && $montoAbonado > $cliente->total_compra) {
        $cambio = $montoAbonado - $cliente->total_compra;
    }

    // Descontar abono
    $cliente->total_compra -= $montoAbonado;
    if ($cliente->total_compra < 0) $cliente->total_compra = 0;
    $cliente->save();

    // REGISTRA EL ABONO COMO VENTA
    $venta = \App\Models\Venta::create([
        'total' => $montoAbonado,
        'pago_recibido' => $montoAbonado,
        'id_usuario' => auth()->id(),
        'metodo_pago' => $request->metodo,
        'tipo' => 'abono',
        'cliente_id' => $cliente->id, // si tienes este campo
    ]);

    // Puedes agregar aquí los detalles de productos si deseas

    // Genera URL de ticket de abono
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
}
