<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    use HasFactory;
    protected $fillable = ['venta_id', 'user_id', 'motivo'];

    public function detalles()
    {
        return $this->hasMany(DevolucionDetalle::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
