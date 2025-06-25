<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'promociones';

    protected $fillable = ['tipo', 'id_categoria', 'id_proveedor', 'id_producto', 'fecha_inicio', 'fecha_fin'];

    public function categoria() {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function proveedor() {
        return $this->belongsTo(Proveedores::class, 'id_proveedor');
    }

    public function productos() {
        return $this->hasMany(Producto::class, 'promocion_id');

    }

    public function producto() {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

}
