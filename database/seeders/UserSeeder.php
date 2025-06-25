<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Andre Ayala',
            'email' => 'andreayala.sanchez@gmail.com',
            'password' => bcrypt('558103445566'),
            'rol' => 'Super-Admin',
        ]);
    }
}
