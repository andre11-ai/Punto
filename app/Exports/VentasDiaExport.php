<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasDiaExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Venta::selectRaw('DATE(created_at) as fecha, SUM(total) as total_vendido, COUNT(*) as ventas_realizadas')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Total Vendido',
            'Ventas Realizadas',
        ];
    }
}
