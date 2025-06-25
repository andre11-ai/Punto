<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDeuda extends Model
{
    protected $table = 'detalle_deudas';
    protected $fillable = [
        'cliente_id',
        'producto_id',
        'precio',
        'cantidad',
        'promocion_aplicada'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
