<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        Sucursal::updateOrCreate(['id' => 1], [
            'nombre' => 'Sucursal Central',
            'direccion' => 'Av. Principal #123',
            'telefono' => '70000000',
            'es_principal' => true,
        ]);
    }
}
