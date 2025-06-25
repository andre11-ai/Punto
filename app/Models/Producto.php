<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
protected $fillable = [
    'codigo',
    'producto',
    'precio_compra',
    'precio_venta',
    'id_categoria',
    'id_proveedor',
    'codigo_barras',
    'foto',
    'promocion_id'
];

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class, 'id_proveedor');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'promocion_id');
    }

}
