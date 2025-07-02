<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $fillable = [
        'venta_id', 'folio', 'rfc', 'razon_social', 'uso_cfdi', 'fecha', 'total'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
