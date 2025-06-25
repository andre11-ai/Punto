<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'telefono',
        'fecha_deuda',
        'total_compra',
        'deuda_inicial'
    ];

    protected $appends = ['dias_sin_pagar'];

    public function detallesDeuda()
    {
        return $this->hasMany(DetalleDeuda::class);
    }

    public function getDiasSinPagarAttribute()
    {
        if (!$this->fecha_deuda) {
            return null;
        }
        return Carbon::parse($this->fecha_deuda)->diffInDays(now());
    }
}
