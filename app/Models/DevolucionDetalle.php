<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionDetalle extends Model
{
    use HasFactory;
    protected $fillable = ['devolucion_id', 'producto_id', 'cantidad', 'precio'];

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
