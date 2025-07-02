<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = ['total','pago_recibido', 'id_usuario', 'metodo_pago', 'tipo','cliente_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }


    public function detalles()
    {
        return $this->hasMany(Detalleventa::class, 'id_venta');
    }

    public function cliente() {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function devoluciones()
    {
        return $this->hasMany(\App\Models\Devolucion::class, 'venta_id');
    }
}
