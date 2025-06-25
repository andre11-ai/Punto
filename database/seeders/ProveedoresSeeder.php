<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Proveedores;
use Faker\Factory as Faker;

class ProveedoresSeeder extends Seeder
{
  public function run()
{
    Proveedores::create([
        'nombre' => 'None',
        'upc' => '0000000000',
    ]);

    $proveedoresReales = [
        'Mascar', 'P&G', 'Unilever', 'Nestlé', 'Coca-Cola',
        'PepsiCo', 'Kellogg\'s', 'Colgate-Palmolive', 'Johnson & Johnson',
        'L\'Oréal', 'Kimberly-Clark', 'General Mills', 'Danone', 'Mars',
        'Mondelez', 'Henkel', 'Reckitt Benckiser', 'Estée Lauder',
        'Clorox', 'Church & Dwight', 'SC Johnson', 'Beiersdorf',
        'Bimbo', 'Grupo Herdez', 'La Costeña', 'Sigma Alimentos',
        'Sabritas', 'Barcel', 'Gamesa', 'Lala'
    ];

    foreach ($proveedoresReales as $proveedor) {
        Proveedores::create([
            'nombre' => $proveedor,
            'upc' => str_pad(mt_rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
        ]);
    }
}
}
