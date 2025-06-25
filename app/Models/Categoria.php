<?php

namespace App\Models;
use App\Models\Proveedor;
use HasFactory;


use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{

    protected $table = 'categorias';

    static $rules = [
        'nombre' => 'required'
    ];

    protected $fillable = [
        'nombre',
        'upc',
        'proveedor_id', 
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class, 'proveedor_id'); // Relación explícita
    }

    public function getFullNameAttribute()
    {
        return $this->nombre . ($this->upc ? " [UPC: {$this->upc}]" : '');
    }

}

