<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasMesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Venta::selectRaw('YEAR(created_at) as anio, MONTH(created_at) as mes, SUM(total) as total_vendido, COUNT(*) as ventas_realizadas')
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Año',
            'Mes',
            'Total Vendido',
            'Ventas Realizadas',
        ];
    }
}
