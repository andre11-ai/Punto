<?php

namespace Database\Seeders;

use App\Models\Compania;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompaniaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Compania::create([
            'nombre' => 'Abarrotes ADS',
            'correo' => 'abarrotesADS@gmail.com ',
            'telefono' => '5520653332',
            'direccion' => 'Mexico, Ciudad de Mexico',
        ]);
    }
}
