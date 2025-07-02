<?php

namespace App\Exports;

use App\Models\Detalleventa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductosVendidosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Detalleventa::selectRaw('
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
    }

    public function headings(): array
    {
        return [
            'ID Producto',
            'Producto',
            'Categoría',
            'Unidades Vendidas',
        ];
    }
}
