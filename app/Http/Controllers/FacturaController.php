<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    // Vista principal de facturación (listado de ventas para facturar)
public function index(Request $request)
{
    $ventas = Venta::with('cliente')
        ->where('tipo', 'VENTA') // Solo ventas tipo VENTA
        ->where('total', '>', 0); // Solo ventas positivas (opcional, si tus devoluciones son negativas)

    if ($request->filled('buscar')) {
        $ventas = $ventas->where(function ($query) use ($request) {
            $query->where('id', $request->buscar)
                ->orWhere('cliente_id', $request->buscar);
        });
    }

    $ventas = $ventas->orderBy('id', 'desc')->paginate(10);

    return view('factura.index', compact('ventas'));
}

    // Formulario para generar factura de una venta
    public function create($ventaId)
    {
        $venta = Venta::with('cliente', 'detalles.producto')->findOrFail($ventaId);
        return view('factura.create', compact('venta'));
    }

    // Guardar la factura
    public function store(Request $request)
{
    $request->validate([
        'venta_id' => 'required|exists:ventas,id',
        'rfc' => 'required',
        'razon_social' => 'required',
        'uso_cfdi' => 'required'
    ]);

    $venta = Venta::findOrFail($request->venta_id);

    // Validar tipo de venta
    if ($venta->tipo !== 'VENTA' || $venta->total <= 0) {
        return back()->with('error', 'Solo se pueden facturar ventas tipo VENTA y con total mayor a cero.');
    }

    $factura = Factura::create([
        'venta_id' => $venta->id,
        'folio' => 'F'.str_pad(Factura::max('id')+1, 6, '0', STR_PAD_LEFT),
        'rfc' => $request->rfc,
        'razon_social' => $request->razon_social,
        'uso_cfdi' => $request->uso_cfdi,
        'fecha' => now(),
        'total' => $venta->total,
    ]);

    return redirect()->route('factura.show', $factura->id)
        ->with('success', 'Factura generada correctamente.')
        ->with('open_pdf', route('factura.pdf', $factura->id));
}

    // Mostrar una sola factura
    public function show(Request $request)
    {
        $facturas = Factura::with('venta.cliente');

        if ($request->filled('buscar')) {
            $facturas = $facturas->where(function ($query) use ($request) {
                $query->where('folio', 'like', '%'.$request->buscar.'%')
                      ->orWhere('razon_social', 'like', '%'.$request->buscar.'%');
            });
        }

        $facturas = $facturas->orderBy('id', 'desc')->paginate(10);

        return view('factura.show', compact('facturas'));
    }

    // Descargar PDF de la factura
    public function pdf($id)
    {
        $factura = Factura::with('venta.detalles.producto', 'venta.cliente')->findOrFail($id);
        $pdf = Pdf::loadView('factura.pdf', compact('factura'));
        return $pdf->download('factura_'.$factura->folio.'.pdf');
    }

    // Mostrar todas las facturas (tabla)
    public function showAll(Request $request)
    {
        $facturas = Factura::with('venta.cliente');

        if ($request->filled('buscar')) {
            $facturas = $facturas->where(function ($query) use ($request) {
                $query->where('folio', 'like', '%'.$request->buscar.'%')
                      ->orWhere('razon_social', 'like', '%'.$request->buscar.'%');
            });
        }

        $facturas = $facturas->orderBy('id', 'desc')->paginate(10);

        return view('factura.showall', compact('facturas'));
    }

    // Visualización tipo CFDI
    public function pdffactura($id)
    {
        $factura = Factura::with('venta.detalles.producto', 'venta.cliente')->findOrFail($id);
        return view('factura.ticket', compact('factura'));
    }

    // Eliminar factura
    public function destroy($id)
    {
        $factura = Factura::findOrFail($id);
        $factura->delete();
        return redirect()->route('factura.showAll')->with('success', 'Factura eliminada correctamente.');
    }

public function ticketFactura($id)
{
    $factura = Factura::with('venta.detalles.producto', 'venta.cliente')->findOrFail($id);
    return \Pdf::loadView('factura.ticket', compact('factura'))
        ->setPaper([0, 150, 5000, 8000], 'portrait') // 80mm x ~175mm, puedes ajustar el alto
        ->setWarnings(false)
        ->stream("ticket_factura_{$factura->folio}.pdf");
}
}
