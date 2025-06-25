<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = ['total','pago_recibido', 'id_usuario', 'metodo_pago', 'tipo'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

// app/Models/Venta.php

public function detalles()
{
    return $this->hasMany(Detalleventa::class, 'id_venta');
}

}
