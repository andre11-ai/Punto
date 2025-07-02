<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasSemanaExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Venta::selectRaw('YEAR(created_at) as anio, WEEK(created_at, 1) as semana, SUM(total) as total_vendido, COUNT(*) as ventas_realizadas')
            ->groupBy('anio', 'semana')
            ->orderBy('anio', 'asc')
            ->orderBy('semana', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Año',
            'Semana',
            'Total Vendido',
            'Ventas Realizadas',
        ];
    }
}
