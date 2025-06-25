<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{

    protected $table = 'proveedores';

    public static $rules = [
        'nombre' => 'required|string|max:255',
        'upc' => 'required|string|max:255',
    ];

    protected $fillable = ['nombre', 'upc'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_proveedor');
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'proveedor_id');
    }
}



