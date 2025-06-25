<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalleventa extends Model
{
  protected $table = 'detalleventa';
  protected $fillable = ['precio', 'cantidad', 'id_producto', 'id_venta' ,'promocion_aplicada'
];

  public function venta()
  {
    return $this->belongsTo(Venta::class);
  }
  public function producto()
{
    return $this->belongsTo(\App\Models\Producto::class, 'id_producto');
}

}
