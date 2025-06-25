<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Proveedores;
use App\Models\Categoria;
use App\Models\Producto;
use Faker\Factory as Faker;

class ArticulosSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        if (Proveedores::count() === 0) {
            $this->call(ProveedoresSeeder::class);
        }
        $proveedores = Proveedores::all();

        $categoriasPrincipales = [
            'Bebidas', 'Lácteos', 'Panadería', 'Carnes', 'Frutas y Verduras',
            'Snacks', 'Cuidado Personal', 'Limpieza', 'Electrónicos', 'Mascotas',
            'Congelados', 'Enlatados', 'Pastas', 'Especias', 'Dulces',
            'Café y Té', 'Cereales', 'Bebidas Alcohólicas', 'Bebé', 'Farmacia',
            'Papelería', 'Juguetes', 'Ropa', 'Zapatos', 'Hogar',
            'Jardinería', 'Automotriz', 'Deportes', 'Libros', 'Electrodomésticos'
        ];

        $usedCodes = [];

        foreach ($categoriasPrincipales as $nombreCategoria) {
            $proveedor = $proveedores->random();

            do {
                $upc = str_pad($faker->unique()->numberBetween(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
            } while (in_array($upc, $usedCodes));

            $usedCodes[] = $upc;

            Categoria::create([
                'nombre' => $nombreCategoria,
                'upc' => $upc,
                'proveedor_id' => $proveedor->id
            ]);
        }

        $categorias = Categoria::all();

        $productos = [
            // Bebidas
            ['Coca-Cola 600ml', 12.50, 15.00, '7501054530015'],
            ['Pepsi 600ml', 11.00, 14.00, '7501054530022'],
            ['Jugo Jumex 1L', 18.00, 22.00, '7501054530039'],
            ['Agua Ciel 1L', 8.50, 12.00, '7501054530046'],
            ['Red Bull 250ml', 25.00, 35.00, '7501054530053'],

            // Lácteos
            ['Leche Lala Entera 1L', 20.00, 25.00, '7501054530060'],
            ['Yogurt Danone Natural', 12.00, 16.00, '7501054530077'],
            ['Queso Panela 200g', 30.00, 40.00, '7501054530084'],
            ['Mantequilla Lurpak 200g', 45.00, 60.00, '7501054530091'],
            ['Crema Lala 250ml', 15.00, 20.00, '7501054530107'],

            // Snacks
            ['Sabritas Original 45g', 10.00, 15.00, '7501054530114'],
            ['Ruffles Queso 45g', 10.00, 15.00, '7501054530121'],
            ['Doritos Nacho 45g', 10.00, 15.00, '7501054530138'],
            ['Cheetos Torciditos 45g', 10.00, 15.00, '7501054530145'],
            ['Cacahuates Japoneses 100g', 12.00, 18.00, '7501054530152'],

            // Cuidado Personal
            ['Shampoo Head & Shoulders', 55.00, 75.00, '7501054530169'],
            ['Jabón Dove Barra', 18.00, 25.00, '7501054530176'],
            ['Pasta Dental Colgate', 22.00, 30.00, '7501054530183'],
            ['Desodorante Axe', 35.00, 45.00, '7501054530190'],
            ['Toallas Fem Sanitas', 25.00, 35.00, '7501054530206'],

            // Limpieza
            ['Cloro Pinol 1L', 20.00, 28.00, '7501054530213'],
            ['Detergente Ariel 1kg', 70.00, 90.00, '7501054530220'],
            ['Jabón Foca 500g', 15.00, 22.00, '7501054530237'],
            ['Desinfectante Lysol', 40.00, 55.00, '7501054530244'],
            ['Escoba Scotch-Brite', 60.00, 80.00, '7501054530251'],

            // Varios
            ['Café Nescafé Clásico 50g', 40.00, 55.00, '7501054530268'],
            ['Galletas Oreo', 15.00, 22.00, '7501054530275'],
            ['Leche en Polvo Nido 400g', 90.00, 120.00, '7501054530282'],
            ['Aceite Capullo 1L', 30.00, 40.00, '7501054530299'],
            ['Arroz SOS 1kg', 20.00, 28.00, '7501054530305']
        ];

        foreach ($productos as $producto) {
            $categoria = $categorias->random();

            Producto::create([
                'codigo' => $producto[3], // Usamos directamente el código de barras
                'producto' => $producto[0],
                'precio_compra' => $producto[1],
                'precio_venta' => $producto[2],
                'id_categoria' => $categoria->id,
                'id_proveedor' => $categoria->proveedor_id,
                'codigo_barras' => $producto[3],
                'foto' => null
            ]);
        }
    }
}
