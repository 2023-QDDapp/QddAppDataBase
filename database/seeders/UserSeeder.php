<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/usuarios.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $modelo = new User();
            $modelo->nombre = $item['nombre'];
            $modelo->telefono = $item['telefono'];
            $modelo->email = $item['email'];
            $modelo->contrasena = $item['contrasena'];
            $modelo->fecha_nacimiento = $item['fecha_nacimiento'];
            $modelo->biografia = $item['biografia'];
            $modelo->foto = $item['foto'];
            $modelo->save();
        }
    }
}