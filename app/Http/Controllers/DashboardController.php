<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Proveedores;
use App\Models\Detalleventa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Exports\ProductosVendidosExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

use App\Exports\VentasDiaExport;
use App\Exports\VentasSemanaExport;
use App\Exports\VentasMesExport;


class DashboardController extends Controller
{
    public function index()
    {
        $totales = [
            'sales' => Venta::count(),
            'products' => Producto::count(),
            'proveedores' => Proveedores::count(),
            'clients' => Cliente::count(),
            'categories' => Categoria::count()
        ];

        $nombresMeses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $ventasPorMeses = Venta::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $ventas = [];
        foreach ($ventasPorMeses as $venta) {
            $year = $venta->year;
            $month = $nombresMeses[$venta->month];
            $ventas[$year][$month] = $venta->total;
        }

        $hoy = Carbon::now();
        $inicioSemana = $hoy->startOfWeek()->toDateString();
        $finSemana = $hoy->endOfWeek()->toDateString();

        $ventasPorSemana = Venta::select(DB::raw('DAYNAME(created_at) as dia'), DB::raw('SUM(total) as total'))
            ->whereBetween('created_at', ["{$inicioSemana} 00:00:00", "{$finSemana} 23:59:59"])
            ->groupBy('dia')
            ->get();

        $ventasPorDia = Venta::select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('SUM(total) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();


        $masVendidos = Detalleventa::select('id_producto', DB::raw('SUM(cantidad) as total_vendidos'))
            ->groupBy('id_producto')
            ->orderByDesc('total_vendidos')
            ->with('producto.categoria')
            ->take(5)
            ->get();

        $actividad = [];
        foreach (Venta::latest()->take(2)->get() as $v) {
            $actividad[] = [
                'tipo' => 'venta',
                'descripcion' => 'Nueva venta',
                'usuario' => $v->user->name ?? 'Sistema',
                'badge' => '$'.number_format($v->total,2),
                'fecha' => $v->created_at,
            ];
        }
        foreach (Cliente::latest()->take(2)->get() as $c) {
            $actividad[] = [
                'tipo' => 'cliente',
                'descripcion' => 'Nuevo cliente',
                'usuario' => $c->user->name ?? 'Registro',
                'badge' => $c->empresa ?? '',
                'fecha' => $c->created_at,
            ];
        }
        foreach (Producto::latest()->take(2)->get() as $p) {
            $actividad[] = [
                'tipo' => 'producto',
                'descripcion' => 'Nuevo producto',
                'usuario' => $p->user->name ?? 'Inventario',
                'badge' => $p->sku,
                'fecha' => $p->created_at,
            ];
        }
        usort($actividad, fn($a,$b) => $b['fecha']->timestamp <=> $a['fecha']->timestamp);
        $actividad = array_slice($actividad, 0, 6);

        return view('dashboard', compact(
            'ventas',
            'ventasPorSemana',
            'ventasPorDia',
            'totales',
            'masVendidos',
            'actividad'
        ));
    }

    public function exportProductosVendidos($formato)
    {
        if ($formato === 'excel') {
            return Excel::download(new ProductosVendidosExport, 'productos_vendidos.xlsx');
        } elseif ($formato === 'pdf') {
            $productos = \App\Models\Detalleventa::selectRaw('
                    detalleventa.id_producto,
                    productos.producto,
                    categorias.nombre as categoria,
                    SUM(detalleventa.cantidad) as cantidad_vendida
                ')
                ->join('productos', 'detalleventa.id_producto', '=', 'productos.id')
                ->join('categorias', 'productos.id_categoria', '=', 'categorias.id')
                ->groupBy('detalleventa.id_producto', 'productos.producto', 'categorias.nombre')
                ->orderByDesc('cantidad_vendida')
                ->get();

            $pdf = PDF::loadView('exports.productos_vendidos_pdf', compact('productos'));
            return $pdf->download('productos_vendidos.pdf');
        } else {
            abort(404);
        }
    }


    public function exportVentasDia($formato)
    {
        if ($formato === 'excel') {
            return Excel::download(new \App\Exports\VentasDiaExport, 'ventas_por_dia.xlsx');
        } elseif ($formato === 'pdf') {
            $ventas = \App\Models\Venta::selectRaw('DATE(created_at) as fecha, SUM(total) as total_vendido, COUNT(*) as ventas_realizadas')
                ->groupBy('fecha')
                ->orderBy('fecha', 'asc')
                ->get();

            $pdf = PDF::loadView('exports.ventas_dia_pdf', compact('ventas'));
            return $pdf->download('ventas_por_dia.pdf');
        } else {
            abort(404);
        }
    }

    public function exportVentasSemana($formato)
    {
        if ($formato === 'excel') {
            return Excel::download(new \App\Exports\VentasSemanaExport, 'ventas_por_semana.xlsx');
        } elseif ($formato === 'pdf') {
            $ventas = \App\Models\Venta::selectRaw('YEAR(created_at) as anio, WEEK(created_at, 1) as semana, SUM(total) as total_vendido, COUNT(*) as ventas_realizadas')
                ->groupBy('anio', 'semana')
                ->orderBy('anio', 'asc')
                ->orderBy('semana', 'asc')
                ->get();

            $pdf = PDF::loadView('exports.ventas_semana_pdf', compact('ventas'));
            return $pdf->download('ventas_por_semana.pdf');
        } else {
            abort(404);
        }
    }

    public function exportVentasMes($formato)
    {
        if ($formato === 'excel') {
            return Excel::download(new \App\Exports\VentasMesExport, 'ventas_por_mes.xlsx');
        } elseif ($formato === 'pdf') {
            $ventas = \App\Models\Venta::selectRaw('YEAR(created_at) as anio, MONTH(created_at) as mes, SUM(total) as total_vendido, COUNT(*) as ventas_realizadas')
                ->groupBy('anio', 'mes')
                ->orderBy('anio', 'asc')
                ->orderBy('mes', 'asc')
                ->get();

            $pdf = PDF::loadView('exports.ventas_mes_pdf', compact('ventas'));
            return $pdf->download('ventas_por_mes.pdf');
        } else {
            abort(404);
        }
    }

}
