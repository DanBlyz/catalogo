<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@tienda.com'],
            [
                'name' => 'Administrador Principal',
                'nombres' => 'Administrador',
                'apellido_paterno' => 'Sistema',
                'apellido_materno' => 'General',
                'cedula' => '12345678',
                'telefono' => '77777777',
                'direccion' => 'Oficina Central',
                'password' => Hash::make('password'),
                'rol_id' => 1,
                'sucursal_id' => 1,
                'estado' => true,
            ]
        );
    }
}
